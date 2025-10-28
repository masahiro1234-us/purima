<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;

class FavoriteController extends Controller
{
    public function __construct()
    {
        // お気に入り操作はログイン必須
        $this->middleware('auth');
    }

    /**
     * お気に入り登録（★にする）
     * ルート: POST /items/{item}/favorite  name=favorite.store
     */
    public function store(Item $item): RedirectResponse
    {
        $user = auth()->user();

        // 二重登録を避けつつ追加（pivot: favorites(user_id, item_id, timestamps)）
        $user->favorites()->syncWithoutDetaching([$item->id]);

        // 元の画面へ戻す（無ければ /?tab=mylist へ）
        return redirect()->to($this->backOrMylist());
    }

    /**
     * お気に入り解除（☆に戻す）
     * ルート: DELETE /items/{item}/favorite  name=favorite.destroy
     */
    public function destroy(Item $item): RedirectResponse
    {
        $user = auth()->user();

        // ピボット行を削除
        $user->favorites()->detach($item->id);

        // 元の画面へ戻す（無ければ /?tab=mylist へ）
        return redirect()->to($this->backOrMylist());
    }

    /**
     * 直前URLが無い/POST先と同じ等の場合は /?tab=mylist をフォールバックに。
     */
    private function backOrMylist(): string
    {
        $previous = url()->previous();         // Referer があればそれを採用
        $current  = url()->current();          // 今のURL（/items/{item}/favorite など）

        if (!$previous || $previous === $current) {
            return route('items.index', ['tab' => 'mylist']);  // ← 設計書どおり
        }
        return $previous;
    }
}