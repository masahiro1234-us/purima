<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','認証')</title>

  {{-- 最小限の共通スタイル --}}
  <link rel="stylesheet" href="{{ asset('css/auth-common.css') }}">
  {{-- 認証ヘッダー専用 --}}
  <link rel="stylesheet" href="{{ asset('css/auth-header.css') }}">

  {{-- 画面別の追加CSS（login / register 等） --}}
  @stack('styles')
</head>
<body>
  {{-- 黒帯＋CTロゴ（SVG） --}}
  @include('components.auth-header')

  {{-- ページ本体 --}}
  <main class="auth-page">
    @yield('content')
  </main>
</body>
</html>