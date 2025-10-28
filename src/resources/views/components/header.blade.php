@php
    $q   = old('q', request('q'));
    $tab = request('tab');

    // マイページ配下はタブを出さない（従来の挙動は維持）
    $isMyPage = request()->routeIs('mypage') || request()->routeIs('mypage.*');

    // 最終判断：親レイアウトからの指示 AND ルート条件
    $renderTabs = ($showTabs ?? true) && !$isMyPage;
@endphp

<header class="site-header">
  <div class="site-header__bar">
    <div class="site-header__inner">
      {{-- ロゴ --}}
      <a href="{{ route('items.index') }}" class="site-header__logo" aria-label="トップへ">
        <img src="{{ asset('image/logo.svg') }}" alt="COACHTECH">
      </a>

      {{-- 検索 --}}
      <form class="site-header__search" action="{{ route('items.index') }}" method="get">
        @if($renderTabs && $tab === 'mylist')
          <input type="hidden" name="tab" value="mylist">
        @endif
        <input type="text" name="q" value="{{ $q }}" placeholder="なにをお探しですか？">
      </form>

      {{-- 右ナビ --}}
      <nav class="site-header__nav" aria-label="ユーティリティナビ">
        @guest
          <a class="site-header__link" href="{{ route('login') }}">ログイン</a>
          <a class="site-header__link" href="{{ route('mypage') }}">マイページ</a>
          <a class="site-header__sell"  href="{{ route('sell.create') }}">出品</a>
        @endguest

        @auth
          <form action="{{ route('logout') }}" method="post" class="site-header__logout-form">
            @csrf
            <button type="submit" class="site-header__link">ログアウト</button>
          </form>
          <a class="site-header__link" href="{{ route('mypage') }}">マイページ</a>
          <a class="site-header__sell"  href="{{ route('sell.create') }}">出品</a>
        @endauth
      </nav>
    </div>
  </div>

  {{-- タブ（おすすめ/マイリスト） --}}
  @if($renderTabs)
    <div class="site-header__tabs">
      <div class="site-header__inner tabs">
        <a href="{{ route('items.index') }}"
           class="tab {{ $tab !== 'mylist' ? 'is-active' : '' }}">おすすめ</a>
        <a href="{{ route('items.index', ['tab' => 'mylist']) }}"
           class="tab {{ $tab === 'mylist' ? 'is-active' : '' }}">マイリスト</a>
      </div>
    </div>
  @endif
</header>