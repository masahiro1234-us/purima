<?php

use Illuminate\Support\Facades\Route;

/* ========= 既存コントローラ ========= */
use App\Http\Controllers\ItemIndexController;
use App\Http\Controllers\ItemShowController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ItemCommentController;
use App\Http\Controllers\MyPageController;

/* ========= 追加コントローラ ========= */
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;

/* ========= 認証系 ========= */
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;

/* ========= トップ・一覧 ========= */
Route::get('/', [ItemIndexController::class, 'index'])->name('items.index');

/* ========= 商品詳細 ========= */
Route::get('/item/{item}', [ItemShowController::class, 'show'])->name('items.show');

/* ========= マイページ ========= */
Route::get('/mypage', [MyPageController::class, 'show'])->name('mypage');
Route::middleware('auth')->group(function () {
    Route::get('/mypage/profile',  [MyPageController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [MyPageController::class, 'update'])->name('mypage.update');
});

/* ========= 出品 ========= */
Route::middleware('auth')->group(function () {
    Route::get('/sell',  [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');
});

/* ========= お気に入り & コメント（要ログイン） ========= */
Route::middleware('auth')->group(function () {
    Route::post('/items/{item}/favorite',   [FavoriteController::class, 'store'])->name('favorite.store');
    Route::delete('/items/{item}/favorite', [FavoriteController::class, 'destroy'])->name('favorite.destroy');
    Route::post('/item/{item}/comments',    [ItemCommentController::class, 'store'])->name('items.comments.store');
});

/* ========= 購入フロー（要ログイン） ========= */
Route::middleware('auth')->group(function () {
    // 確認画面
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');

    // 支払い方法の更新
    Route::post('/purchase/{item}/method', [PurchaseController::class, 'updateMethod'])->name('purchase.method');

    // 配送先 変更ページ -> 更新
    Route::get('/purchase/{item}/address',  [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/{item}/address', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // 購入確定
    Route::post('/purchase/{item}/buy', [PurchaseController::class, 'buy'])->name('purchase.buy');
});

/* ========= 認 証 ========= */
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

Route::get('/email/verify', [VerificationController::class, 'notice'])
    ->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//  === 画像配信用フォールバック（public/storage を辿れない環境向け） ===
// /storage/... で来たリクエストを storage/app/public/... の実ファイルから配信する
Route::get('/storage/{path}', function (string $path) {
    $full = storage_path('app/public/' . $path);
    if (!is_file($full)) {
        abort(404);
    }
    // キャッシュ系ヘッダを軽く付与（任意）
    return response()->file($full, [
        'Cache-Control' => 'public, max-age=31536000',
        'Content-Type'  => \Illuminate\Support\Facades\File::mimeType($full) ?? 'application/octet-stream',
    ]);
})->where('path', '.*');

// カード決済（Stripe Checkout へ）
Route::post('/purchase/{item}/card', [PurchaseController::class, 'cardCheckout'])->name('purchase.card');

// 決済後の戻り先
Route::get('/purchase/stripe/success', [PurchaseController::class, 'stripeSuccess'])->name('purchase.stripe.success');
Route::get('/purchase/stripe/cancel',  [PurchaseController::class, 'stripeCancel'])->name('purchase.stripe.cancel');