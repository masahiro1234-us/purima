@extends('layouts.auth')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('title','ログイン | Prima')

@section('content')
<section class="auth">
  <div class="auth__card">
    <h1 class="auth__title">ログイン</h1>

    @if ($errors->any())
      <div class="auth__error">
        @foreach ($errors->all() as $e)
          <div>{{ $e }}</div>
        @endforeach
      </div>
    @endif

    <form method="post" action="{{ route('login.attempt') }}" class="auth__form" novalidate>
      @csrf

      <label class="auth__label">メールアドレス
        <input type="email" name="email" value="{{ old('email') }}" class="auth__input" autocomplete="email">
      </label>

      <label class="auth__label">パスワード
        <input type="password" name="password" class="auth__input" autocomplete="current-password">
      </label>

      <button type="submit" class="auth__primary">ログインする</button>

      <div class="auth__link-row">
        <a href="{{ route('register') }}" class="auth__link">会員登録はこちら</a>
      </div>
    </form>
  </div>
</section>
@endsection