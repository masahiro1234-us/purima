<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細ページが表示される()
    {
        $item = Item::query()->create([
            'title' => '腕時計',
            'price' => 1500,
            'description' => '説明',
            'img_url' => 'https://placehold.co/600x600',
            'status' => 'public',
        ]);

        $this->get(route('items.show', $item))
            ->assertOk()
            ->assertSee('腕時計')
            ->assertSee('¥1,500');
    }
}