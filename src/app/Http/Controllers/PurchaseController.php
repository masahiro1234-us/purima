<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Stripe\StripeClient; // ← 追加（Stripe SDK）

class PurchaseController extends Controller
{
    /**
     * 購入確認画面
     */
    public function show(Request $request, Item $item)
    {
        // 売却済みなら詳細へ戻す
        if ($item->buyer_id) {
            return redirect()->route('items.show', $item)->with('error', 'この商品は売り切れです。');
        }

        // 支払い方法はセッションから（なければデフォルト：コンビニ払い）
        $method = session("purchase.{$item->id}.method", 'コンビニ払い');

        return view('purchase.show', [
            'item'   => $item,
            'method' => $method,
            'user'   => $request->user(),
        ]);
    }

    /**
     * 支払い方法の更新
     */
    public function updateMethod(Request $request, Item $item)
    {
        $request->validate([
            'method' => ['required', 'in:コンビニ払い,カード支払い'],
        ]);

        $method = $request->input('method');
        session(["purchase.{$item->id}.method" => $method]);

        return redirect()->route('purchase.show', $item);
    }

    /**
     * 配送先編集画面
     */
    public function editAddress(Request $request, Item $item)
    {
        return view('purchase.address', [
            'item' => $item,
            'user' => $request->user(),
        ]);
    }

    /**
     * 配送先更新
     */
    public function updateAddress(Request $request, Item $item)
    {
        $data = $request->validate([
            'postal_code' => ['required', 'string', 'max:20'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $user->fill($data)->save();

        return redirect()->route('purchase.show', $item)->with('status', '配送先を更新しました。');
    }

    /**
     * コンビニ払いなど（従来の即時購入確定）
     */
    public function buy(Request $request, Item $item)
    {
        if ($item->buyer_id) {
            return redirect()->route('items.show', $item)->with('error', 'この商品は売り切れです。');
        }

        $method = session("purchase.{$item->id}.method", 'コンビニ払い');

        // 購入＝buyer_id を自分に更新
        $item->buyer_id = $request->user()->id;
        $item->save();

        // セッション掃除
        session()->forget("purchase.{$item->id}");

        return redirect()->route('items.show', $item)->with('status', "購入が完了しました（{$method}）");
    }

    /* =========================
     |  以下、Stripe（カード支払い）
     |  Webhook なしの最小構成
     ==========================*/

    /**
     * Stripe Checkout セッション作成 → リダイレクト
     */
    public function cardCheckout(Request $request, Item $item)
    {
        if ($item->buyer_id) {
            return redirect()->route('items.show', $item)->with('error', 'この商品は売り切れです。');
        }

        // Stripe クライアント
        $stripe = new StripeClient(config('services.stripe.secret'));

        // 戻り先
        $successUrl = route('purchase.stripe.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = route('purchase.stripe.cancel', [], true);

        // Checkout セッション作成
        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price * 100, // 円 → セント単位
                    'product_data' => [
                        'name' => $item->title,
                        // 画像URL（任意／相対パスなら絶対URLに）
                        'images' => [url($item->img_url)],
                    ],
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $request->user()->email,
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'metadata' => [
                'item_id' => (string)$item->id,
                'user_id' => (string)$request->user()->id,
            ],
        ]);

        // セッションIDを控えておく（簡易照合用）
        $request->session()->put('purchase.pending_session', $session->id);

        // Stripe のホストされた決済画面へ
        return redirect()->away($session->url);
    }

    /**
     * 決済成功（Webhook なしの簡易検証）
     */
    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        $pending   = $request->session()->pull('purchase.pending_session'); // 取り出して消す

        if (!$sessionId || !$pending || $sessionId !== $pending) {
            return redirect()->route('items.index')->with('error', '決済セッションが見つかりません。');
        }

        $stripe  = new StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sessionId, []);

        // 決済完了の簡易チェック
        if (($session->status ?? null) !== 'complete' && ($session->payment_status ?? null) !== 'paid') {
            return redirect()->route('items.index')->with('error', '決済が完了しませんでした。');
        }

        $itemId = (int)($session->metadata->item_id ?? 0);
        $userId = (int)($session->metadata->user_id ?? 0);

        $item = Item::find($itemId);
        if (!$item || $item->buyer_id) {
            return redirect()->route('items.index')->with('error', '商品情報を取得できませんでした。');
        }

        // 成功後に購入確定
        $item->buyer_id = $userId ?: auth()->id();
        $item->save();

        return redirect()->route('items.show', $item)->with('status', 'カード決済が完了しました。');
    }

    /**
     * 決済キャンセル
     */
    public function stripeCancel(Request $request)
    {
        return back()->with('error', '決済をキャンセルしました。');
    }
}