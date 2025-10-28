<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 一覧ページが表示され商品名が見える()
    {
        $item = Item::query()->create([
            'title' => 'テスト商品',
            'price' => 1234,
            'description' => '説明',
            'img_url' => 'https://placehold.co/600x600',
            'status' => 'public',
        ]);

        $res = $this->get(route('items.index'));
        $res->assertOk()->assertSee('テスト商品');
    }
}