<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class SellController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** 出品フォーム */
    public function create()
    {
        return view('sell.create');
    }

    /** 出品登録 */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'price'       => ['required', 'integer', 'min:1'],
            'brand'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image'       => ['nullable', 'image', 'max:4096'],
            // フォームで入る場合も想定して一応許可。未送信なら下でデフォルト補完
            'status'      => ['nullable', 'string', 'max:255'],
        ]);

        // 画像保存（storage/app/public/items -> /storage/items/...）
        $imgUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public'); // => items/xxxx.jpg
            $imgUrl = '/storage/' . $path;                              // => /storage/items/xxxx.jpg
        }

        // NOT NULL の status を補完（テストでは未送信なのでここが重要）
        $status = $data['status'] ?? '良好';

        $item = new Item();
        $item->title       = $data['title'];
        $item->price       = $data['price'];
        $item->brand       = $data['brand'] ?? null;
        $item->description = $data['description'] ?? null;
        $item->status      = $status;
        $item->user_id     = $request->user()->id;
        $item->img_url     = $imgUrl; // 画像未添付なら null のままでOK（既存URL品は別経路）

        $item->save();

        return redirect()->route('items.show', $item)->with('status', '出品しました。');
    }
}