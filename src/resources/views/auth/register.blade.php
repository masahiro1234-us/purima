@extends('layouts.auth')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

@section('title','会員登録 | Prima')

@section('content')
<section class="auth">
  <div class="auth__card">
    <h1 class="auth__title">会員登録</h1>

    @if ($errors->any())
      <div class="auth__error">
        @foreach ($errors->all() as $e)
          <div>{{ $e }}</div>
        @endforeach
      </div>
    @endif

    <form method="post" action="{{ route('register.store') }}" class="auth__form" novalidate>
      @csrf

      <label class="auth__label">ユーザー名
        <input type="text" name="name" value="{{ old('name') }}" class="auth__input" autocomplete="name">
      </label>

      <label class="auth__label">メールアドレス
        <input type="email" name="email" value="{{ old('email') }}" class="auth__input" autocomplete="email">
      </label>

      <label class="auth__label">パスワード
        <input type="password" name="password" class="auth__input" autocomplete="new-password">
      </label>

      <label class="auth__label">確認用パスワード
        <input type="password" name="password_confirmation" class="auth__input" autocomplete="new-password">
      </label>

      <button type="submit" class="auth__primary">登録する</button>

      <div class="auth__link-row">
        <a href="{{ route('login') }}" class="auth__link">ログインはこちら</a>
      </div>
    </form>
  </div>
</section>
@endsection