{{-- resources/views/purchase/show.blade.php --}}
@extends('layouts.app')
@section('title','商品購入画面')
@php($showTabs = false)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endpush

@section('content')
<section class="purchase">

  {{-- 左ペイン --}}
  <div class="purchase__pane purchase__pane--left">

    {{-- 商品情報（枠なし・下に細線） --}}
    <div class="purchase__item purchase__item--plain">
      <div class="purchase__thumb">
        {{-- 画像は img_url で統一 --}}
        <img src="{{ $item->img_url }}" alt="{{ $item->title }}">
      </div>
      <div class="purchase__meta">
        <div class="purchase__name">{{ $item->title }}</div>
        <div class="purchase__price">¥{{ number_format($item->price) }}</div>
      </div>
    </div>

    {{-- 支払い方法（セレクト変更で即反映） --}}
    <section class="purchase__section">
      <div class="purchase__label">支払い方法</div>
      <form method="post" action="{{ route('purchase.method', $item) }}" class="purchase__method-form">
        @csrf
        <select name="method" class="purchase__select" onchange="this.form.submit()">
          <option value="コンビニ払い" {{ $method === 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
          <option value="カード支払い" {{ $method === 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
        </select>
      </form>
      @error('method')
        <p class="form-error">{{ $message }}</p>
      @enderror
    </section>

    {{-- 配送先（見出し右に「変更する」リンク） --}}
    <section class="purchase__section">
      <div class="purchase__label-row">
        <div class="purchase__label">配送先</div>
        <a href="{{ route('purchase.address.edit', $item) }}" class="purchase__link">変更する</a>
      </div>
      <div class="purchase__addr">
        <div>〒 {{ $user->postal_code ?? 'XXX-YYYY' }}</div>
        <div>{{ $user->address ?? 'ここには住所と建物が入ります' }}</div>
        @if($user->building)
          <div>{{ $user->building }}</div>
        @endif
      </div>
    </section>

  </div>

  {{-- 右ペイン：情報ボックス＋購入ボタン --}}
  <aside class="purchase__pane purchase__pane--right">
    <div class="summary">
      <div class="summary__row">
        <span>商品代金</span>
        <span>¥{{ number_format($item->price) }}</span>
      </div>
      <div class="summary__row">
        <span>支払い方法</span>
        <span>{{ $method }}</span>
      </div>
    </div>

    {{-- 購入ボタン（カード支払いは Stripe へ / それ以外は従来どおり buy） --}}
    @if($method === 'カード支払い')
      <form method="post" action="{{ route('purchase.card', $item) }}" class="summary-cta">
        @csrf
        <button class="summary__buy">カードで支払う</button>
      </form>
    @else
      <form method="post" action="{{ route('purchase.buy', $item) }}" class="summary-cta">
        @csrf
        <button class="summary__buy">購入する</button>
      </form>
    @endif
  </aside>

</section>
@endsection