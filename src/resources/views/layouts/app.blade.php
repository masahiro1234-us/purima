{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Prima')</title>

    {{-- 共通スタイル --}}
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    {{-- ページ固有スタイルを挿入（個別ビューで @push('styles') を使う） --}}
    @stack('styles')

    {{-- 最終的な上書き・レスポンシブ調整用スタイル（ここは必ず最後に） --}}
    <link rel="stylesheet" href="{{ asset('css/responsive_fix.css') }}">
</head>
<body>
    {{-- 子ビューから $showTabs が指定されていなければ true 扱い --}}
    @include('components.header', ['showTabs' => $showTabs ?? true])

    <main class="page">
        @yield('content')
    </main>
</body>
</html>