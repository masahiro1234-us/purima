<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function コンビニ払いで購入完了まで進む_Stripeは触らない()
    {
        $user = User::factory()->create();
        $item = Item::query()->create([
            'title'=>'商品X','price'=>3000,'description'=>'d',
            'img_url'=>'https://placehold.co/600','status'=>'public'
        ]);
        $this->actingAs($user);

        // 支払い方法を「コンビニ払い」に更新
        $this->post(route('purchase.method', $item), ['method'=>'コンビニ払い'])
             ->assertRedirect(route('purchase.show', $item));

        // 購入実行（buyer_id が自分に付く）
        $this->post(route('purchase.buy', $item))->assertRedirect(route('items.show', $item));

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'buyer_id' => $user->id,
        ]);
    }
}