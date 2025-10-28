<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthGuardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 非ログインはマイページに入れずログインへ飛ばされる()
    {
        $this->get(route('mypage'))
            ->assertRedirect(route('login'));
    }
}