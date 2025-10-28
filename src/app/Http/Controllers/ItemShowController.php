<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemShowController extends Controller
{
    public function show(Item $item)
    {
        // ★ お気に入り・コメントの両方を最新件数で付与
        $item->loadCount(['favorites', 'comments']);

        return view('items.show', [
            'item'            => $item,
            'displayImageUrl' => $item->img_url,
            // ★ ここはコントローラで実数を渡す
            'favoritesCount'  => $item->favorites_count,
            'commentCount'    => $item->comments_count,
        ]);
    }
}