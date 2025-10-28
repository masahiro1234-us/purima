<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemCommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        // バリデーション
        $data = $request->validate([
            'content' => ['required', 'string', 'max:255'],
        ]);

        // ログイン必須（保険）
        $userId = auth()->id();
        if (!$userId) {
            return redirect()->route('login')->withErrors(['login' => 'ログインが必要です。']);
        }

        // 紐づけて作成
        $item->comments()->create([
            'user_id' => $userId,
            'content' => $data['content'],
        ]);

        return back();
    }
}