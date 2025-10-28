<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    // 認証メールの案内
    public function notice()
    {
        return view('auth.verify-email');
    }

    // 本認証
    public function verify(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            // 既に認証済みならそのままマイページ編集へ
            return redirect()->route('mypage.edit');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // ★ここを profile.edit から mypage.edit へ
        return redirect()->route('mypage.edit')->with('verified', true);
    }

    // 再送
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('mypage.edit');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}