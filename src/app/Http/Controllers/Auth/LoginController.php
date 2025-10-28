<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct()
    {
        // 未ログイン者だけアクセス可（/logout は除外）
        $this->middleware('guest')->except(['logout']);
    }

    /**
     * ログインフォーム表示 (GET /login)
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ログイン実行 (POST /login)
     */
    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt($cred, false)) {
            return back()
                ->withErrors(['email' => 'メールアドレスまたはパスワードが正しくありません。'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        // 未認証（メール未確認）なら認証案内へ
        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // ▼プロフィール未完了ならマイページの編集画面へ
        if (! $request->user()->profile_completed) {
            return redirect()->route('mypage.edit');   // ← 修正済み
        }

        // 直前の intended があればそこへ、なければトップへ
        return redirect()->intended(route('items.index'));
    }

    /**
     * ログアウト (POST /logout)
     */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('items.index');
    }
}