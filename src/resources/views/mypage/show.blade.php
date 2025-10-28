@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/mypage_show.css') }}">
@endpush

@section('content')
<div class="mypage">
  <div class="mypage__header">
    <div class="mypage__header-inner">

      {{-- ★プロフィール画像だけ修正（出品画像は一切変更しない） --}}
      <div class="mypage__avatar">
        @if($user->avatar_url)
          <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" loading="lazy" decoding="async">
        @else
          <div class="mypage__avatar--placeholder" aria-hidden="true"></div>
        @endif
      </div>

      <div class="mypage__name">{{ $user->name }}</div>
      <a href="{{ route('mypage.edit') }}" class="mypage__edit-btn">プロフィールを編集</a>
    </div>
  </div>

  <div class="mypage__tabs">
    <a class="mypage__tab {{ $activeTab === 'listed' ? 'is-active' : '' }}"
       href="{{ route('mypage', ['tab' => 'listed']) }}">出品した商品</a>
    <a class="mypage__tab {{ $activeTab === 'purchased' ? 'is-active' : '' }}"
       href="{{ route('mypage', ['tab' => 'purchased']) }}">購入した商品</a>
  </div>

  @if ($activeTab === 'listed')
    <div class="mypage__products">
      @forelse ($listed as $item)
        <a href="{{ route('items.show', $item) }}" class="mypage__product">
          <div class="mypage__product-thumb">
            {{-- 出品画像は従来通り img_url を使用 --}}
            <img src="{{ $item->img_url }}" alt="{{ $item->title }}" loading="lazy" decoding="async">
          </div>
          <div class="mypage__product-name">{{ $item->title }}</div>
        </a>
      @empty
        <p class="mypage__empty">出品した商品はまだありません。</p>
      @endforelse
    </div>
    <div class="mypage__pager">
      {{ $listed->links() }}
    </div>
  @endif

  @if ($activeTab === 'purchased')
    <div class="mypage__products">
      @forelse ($purchased as $item)
        <a href="{{ route('items.show', $item) }}" class="mypage__product">
          <div class="mypage__product-thumb">
            {{-- こちらも従来通り --}}
            <img src="{{ $item->img_url }}" alt="{{ $item->title }}" loading="lazy" decoding="async">
          </div>
          <div class="mypage__product-name">{{ $item->title }}</div>
        </a>
      @empty
        <p class="mypage__empty">購入した商品はまだありません。</p>
      @endforelse
    </div>
    <div class="mypage__pager">
      {{ $purchased->links() }}
    </div>
  @endif
</div>
@endsection