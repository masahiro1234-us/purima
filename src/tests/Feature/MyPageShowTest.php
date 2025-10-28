<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyPageShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン後にマイページが表示できる()
    {
        $user = User::factory()->create(['name' => '金井']);
        $this->actingAs($user);

        // 出品1件・購入1件（buyer_id）
        $listed = Item::query()->create([
            'title' => '出品A', 'price' => 1000, 'description' => 'd',
            'img_url' => 'https://placehold.co/600', 'status' => 'public',
            'user_id' => $user->id,
        ]);
        $purchased = Item::query()->create([
            'title' => '購入B', 'price' => 2000, 'description' => 'd',
            'img_url' => 'https://placehold.co/600', 'status' => 'public',
            'buyer_id' => $user->id,
        ]);

        $this->get(route('mypage', ['tab' => 'listed']))
            ->assertOk()
            ->assertSee('出品した商品')
            ->assertSee('出品A');

        $this->get(route('mypage', ['tab' => 'purchased']))
            ->assertOk()
            ->assertSee('購入した商品')
            ->assertSee('購入B');
    }
}