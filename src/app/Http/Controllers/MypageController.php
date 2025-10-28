<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class MyPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // 全アクションでログイン必須
    }

    /**
     * マイページ表示
     * GET /mypage  (?tab=listed|purchased)
     */
    public function show(Request $request)
    {
        $user      = $request->user();
        $activeTab = $request->query('tab', 'listed'); // 既定は出品した商品

        $hasUserId  = Schema::hasColumn('items', 'user_id');
        $hasBuyerId = Schema::hasColumn('items', 'buyer_id');

        // 出品した商品
        if ($hasUserId) {
            $listed = Item::where('user_id', $user->id)
                ->latest('created_at')
                ->paginate(12, ['*'], 'listed_page')
                ->appends(['tab' => 'listed']);
        } else {
            $listed = $this->emptyPaginator('listed_page');
        }

        // 購入した商品
        if ($hasBuyerId) {
            $purchased = Item::where('buyer_id', $user->id)
                ->latest('created_at')
                ->paginate(12, ['*'], 'purchased_page')
                ->appends(['tab' => 'purchased']);
        } else {
            $purchased = $this->emptyPaginator('purchased_page');
        }

        return view('mypage.show', [
            'user'      => $user,
            'listed'    => $listed,
            'purchased' => $purchased,
            'activeTab' => $activeTab,
        ]);
    }

    /**
     * プロフィール編集画面
     * GET /mypage/profile
     */
    public function edit(Request $request)
    {
        return view('mypage.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * プロフィール更新
     * POST /mypage/profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address'     => ['nullable', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'avatar'      => ['nullable', 'image', 'max:4096'],
        ]);

        // 画像が来たら storage/app/public/avatars に保存
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public'); // 例: avatars/xxxx.jpg
            // public/storage/avatars/xxxx.jpg を指す相対パスを保存（User::avatar_src が解決）
            $user->avatar_path = 'storage/' . $path;                      // 例: storage/avatars/xxxx.jpg
        }

        $user->fill([
            'name'        => $data['name'],
            'postal_code' => $data['postal_code'] ?? null,
            'address'     => $data['address'] ?? null,
            'building'    => $data['building'] ?? null,
        ]);

        if (property_exists($user, 'profile_completed')) {
            $user->profile_completed = true;
        }

        $user->save();

        return redirect()->route('mypage')->with('status', 'プロフィールを更新しました。');
    }

    /**
     * 空の LengthAwarePaginator を返す（安全策）
     */
    private function emptyPaginator(string $pageName): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            items: [],
            total: 0,
            perPage: 12,
            currentPage: 1,
            options: [
                'path'     => url()->current(),
                'pageName' => $pageName,
            ]
        );
    }
}