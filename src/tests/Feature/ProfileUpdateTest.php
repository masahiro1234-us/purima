<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィールを画像付きで更新できる()
    {
        Storage::fake('public');

        $user = User::factory()->create(['name' => '旧名']);
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $res = $this->post(route('mypage.update'), [
            'name' => '新しい名前',
            'postal_code' => '123-4567',
            'address' => 'テスト市1-2-3',
            'building' => 'ビル101',
            'avatar' => $file,
        ]);

        $res->assertRedirect(route('mypage'));
        $user->refresh();

        $this->assertSame('新しい名前', $user->name);
        // 保存されたか（パスの先頭だけ確認）
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $user->avatar_path));
    }
}