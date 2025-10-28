<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインユーザーがコメントできる()
    {
        $user = User::factory()->create();
        $item = Item::query()->create([
            'title'=>'AAA','price'=>100,'description'=>'d',
            'img_url'=>'https://placehold.co/600','status'=>'public'
        ]);

        $this->actingAs($user);

        $res = $this->post(route('items.comments.store', $item), [
            'content' => '良さそう！',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => '良さそう！',
        ]);
    }
}