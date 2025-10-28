<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct()
    {
        // ログインしている人はアクセス不可（※ログアウトした上でアクセス）
        $this->middleware('guest');
    }

    /**
     * 会員登録フォーム表示
     * GET /register
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * 会員登録処理
     * POST /register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // パスワードは必ずここでハッシュ（バージョン差に依らず安全）
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // これで認証メールが自動送信される（User が MustVerifyEmail 実装している前提）
        event(new Registered($user));

        // 必要なら自動ログイン
        auth()->login($user);

        // 誘導画面へ（Figma の「メール認証誘導画面」に相当）
        return redirect()->route('verification.notice');
    }
}