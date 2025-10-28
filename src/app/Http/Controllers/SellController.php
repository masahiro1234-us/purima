<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class SellController extends Controller
{
    public function create()
    {
        return view('sell.create'); // 既存の出品フォーム
    }

    public function store(Request $request)
    {
        // 最低限のバリデーション例（必要に応じて拡張してください）
        $validated = $request->validate([
            'title'       => ['required','string','max:255'],
            'price'       => ['required','integer','min:0'],
            'brand'       => ['nullable','string','max:255'],
            'description' => ['nullable','string','max:2000'],
            // 画像は「ファイル」か「外部URL」のどちらかが来る運用を想定
            'image'       => ['nullable','image','max:4096'],
            'img_url'     => ['nullable','url'], // 外部URLを許可
            'status'      => ['nullable','string','max:255'],
        ]);

        $item = new Item();
        $item->fill([
            'title'       => $validated['title'],
            'price'       => $validated['price'],
            'brand'       => $validated['brand']       ?? null,
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status']      ?? null,
        ]);
        $item->user_id = Auth::id();

        // 画像の保存ロジック：①ファイルがあれば storage(public) に保存し、/storage/... でDBに格納
        if ($request->hasFile('image')) {
            // storage/app/public/items/xxxx.jpg
            $path = $request->file('image')->store('items', 'public');
            // ブラウザが辿れるURLで保存（先頭に / を付ける）
            $item->img_url = '/storage/' . $path;  // ← 例: /storage/items/abc.jpg
        }
        // ②ファイルが無く、外部URLが来ていたらそのまま保存
        elseif (!empty($validated['img_url'])) {
            $item->img_url = $validated['img_url']; // http/https のURL
        }
        // ③どちらも無いなら null（ダミーを出したくない要望に合わせてnoimageは使いません）
        else {
            $item->img_url = null;
        }

        $item->save();

        return redirect()->route('items.show', $item)->with('status', '出品しました');
    }
}