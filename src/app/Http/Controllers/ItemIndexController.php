<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ItemIndexController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');   // 'mylist' | null
        $q   = $request->query('q');

        // ===== マイリスト =====
        if ($tab === 'mylist') {
            if (auth()->check()) {
                // ログイン済み: お気に入りした商品だけ
                $items = Item::withCount('favorites') // いいね数を使うなら
                    ->whereHas('favorites', function ($users) {
                        // belongsToMany(User) なので users テーブルの主キーで絞る
                        $users->whereKey(auth()->id());       // users.id = auth()->id()
                        // あるいは $users->where('users.id', auth()->id());
                    })
                    ->latest()
                    ->paginate(12)
                    ->withQueryString();
            } else {
                // 未ログイン: 何も表示しない（空のページネータ）
                $items = new LengthAwarePaginator(
                    collect([]), // items
                    0,           // total
                    12,          // per page
                    LengthAwarePaginator::resolveCurrentPage(),
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            }

            return view('items.index', [
                'items'     => $items,
                'activeTab' => 'mylist',
                'q'         => $q,
            ]);
        }

        // ===== おすすめ（通常一覧） =====
        $query = Item::query()->withCount('favorites');

        if ($q) {
            $query->where('title', 'like', "%{$q}%");
        }

        $items = $query->latest()->paginate(12)->withQueryString();

        return view('items.index', [
            'items'     => $items,
            'activeTab' => 'recommend',
            'q'         => $q,
        ]);
    }
}