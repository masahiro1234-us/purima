<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteToggleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function お気に入りを追加して外せる()
    {
        $user = User::factory()->create();
        $item = Item::query()->create([
            'title'=>'Fav','price'=>100,'description'=>'d',
            'img_url'=>'https://placehold.co/600','status'=>'public'
        ]);
        $this->actingAs($user);

        $this->post(route('favorite.store', $item))->assertRedirect();
        $this->assertDatabaseHas('favorites', ['user_id'=>$user->id,'item_id'=>$item->id]);

        $this->delete(route('favorite.destroy', $item))->assertRedirect();
        $this->assertDatabaseMissing('favorites', ['user_id'=>$user->id,'item_id'=>$item->id]);
    }
}