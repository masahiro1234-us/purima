{{-- resources/views/items/show.blade.php --}}
@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
<link rel="stylesheet" href="{{ asset('css/responsive_fix.css') }}">
@endpush

@section('content')
<section class="pd page-item-show">
  <div class="pd__grid">

    {{-- 出品画像（既存どおり img_url を使用） --}}
    <figure class="pd__image {{ $item->buyer_id ? 'is-sold' : '' }}">
      <img
        src="{{ $item->img_url }}"
        alt="{{ $item->title }}"
        width="570"
        height="570"
        loading="lazy"
        decoding="async"
      >
      @if(!is_null($item->buyer_id ?? null))
        <div class="pd__soldmark" aria-hidden="true">SOLD</div>
      @endif
    </figure>

    <div class="pd__main">
      <h1 class="pd__title">{{ $item->title }}</h1>
      <div class="pd__brand">{{ $item->brand ?? 'ブランド不明' }}</div>
      <div class="pd__price">
        ¥{{ number_format($item->price) }} <span class="pd__tax">(税込)</span>
      </div>

      {{-- アクション（お気に入り / コメント） --}}
      <div class="pd__actions">
        <div class="pd__action" aria-label="お気に入り">
          @auth
            @php $faved = $item->isFavoritedBy(auth()->user()); @endphp
            @if ($faved)
              <form action="{{ route('favorite.destroy', $item) }}" method="POST" class="pd__icon-box">
                @csrf
                @method('DELETE')
                <button type="submit" aria-label="お気に入り解除" class="pd__icon-btn">
                  <img src="{{ asset('image/okiniiri.png') }}" alt="" class="pd__icon">
                </button>
              </form>
            @else
              <form action="{{ route('favorite.store', $item) }}" method="POST" class="pd__icon-box">
                @csrf
                <button type="submit" aria-label="お気に入りに追加" class="pd__icon-btn">
                  <img src="{{ asset('image/okiniiri.png') }}" alt="" class="pd__icon is-inactive">
                </button>
              </form>
            @endif
          @else
            <span class="pd__icon-box">
              <span class="pd__icon-btn" aria-hidden="true">
                <img src="{{ asset('image/okiniiri.png') }}" alt="" class="pd__icon is-inactive">
              </span>
            </span>
          @endauth
          <span class="pd__count">{{ $item->favoritesCount() }}</span>
        </div>

        <div class="pd__action" aria-label="コメント件数">
          <span class="pd__icon-box">
            <span class="pd__icon-btn" aria-hidden="true">
              <img src="{{ asset('image/comment.png') }}" alt="" class="pd__icon is-inactive">
            </span>
          </span>
          <span class="pd__count">{{ $item->comments_count ?? $item->comments()->count() }}</span>
        </div>
      </div>

      @php $sold = !is_null($item->buyer_id ?? null); @endphp
      @if($sold)
        <div class="pd__buybtn is-sold" aria-disabled="true">SOLD</div>
      @else
        <a href="{{ route('purchase.show', $item) }}" class="pd__buybtn">購入手続きへ</a>
      @endif

      {{-- 商品説明 --}}
      <section class="pd__block">
        <h2 class="pd__section-title">商品説明</h2>
        <div class="pd__desc">{{ $item->description }}</div>
      </section>

      {{-- 商品の情報 --}}
      <section class="pd__block">
        <h2 class="pd__section-title">商品の情報</h2>
        <div class="pd__info">
          <div class="pd__info-row">
            <div class="pd__info-label">カテゴリー</div>
            <div class="pd__info-body">
              @php
                $cats = (method_exists($item,'categories') && !empty($item->categories))
                        ? $item->categories
                        : collect();
              @endphp
              @if($cats->count())
                @foreach($cats as $cat)
                  <span class="pd__chip">{{ $cat->name }}</span>
                @endforeach
              @else
                <span class="pd__chip">洋服</span>
                <span class="pd__chip">メンズ</span>
              @endif
            </div>
          </div>

          <div class="pd__info-row">
            <div class="pd__info-label">商品の状態</div>
            <div class="pd__info-body">
              <span class="pd__cond-text">{{ $item->condition ?? '良好' }}</span>
            </div>
          </div>
        </div>
      </section>

      {{-- コメント --}}
      <section class="pd__block">
        <h2 class="pd__section-title">
          コメント({{ $item->comments_count ?? $item->comments()->count() }})
        </h2>

        @php $comments = $item->comments()->latest()->get(); @endphp

        <div class="pd__comments">
          @forelse ($comments as $comment)
            <div class="pd__comment">
              {{-- ★プロフィール画像（出品画像とは無関係） --}}
              <div class="pd__avatar">
                @if(!empty($comment->user?->avatar_url))
                  <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" loading="lazy" decoding="async">
                @else
                  <div class="pd__avatar--placeholder" aria-hidden="true"></div>
                @endif
              </div>
              <div class="pd__comment-right">
                <div class="pd__name">{{ $comment->user->name ?? 'ユーザー' }}</div>
                <div class="pd__bubble">{{ $comment->content }}</div>
              </div>
            </div>
          @empty
            <div class="pd__comment">
              <div class="pd__avatar">
                @if(auth()->check() && !empty(auth()->user()->avatar_url))
                  <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" loading="lazy" decoding="async">
                @else
                  <div class="pd__avatar--placeholder" aria-hidden="true"></div>
                @endif
              </div>
              <div class="pd__comment-right">
                <div class="pd__name">{{ auth()->check() ? auth()->user()->name : 'ユーザー名' }}</div>
                <div class="pd__bubble">こちらにコメントが入ります。</div>
              </div>
            </div>
          @endforelse
        </div>

        <form class="pd__form" method="post" action="{{ route('items.comments.store', $item) }}">
          @csrf
          <label class="pd__comment-label" for="content">商品へのコメント</label>
          @guest
            <textarea id="content" class="pd__textarea" rows="6" maxlength="255" placeholder="コメントを入力" disabled></textarea>
            <button type="button" class="pd__submit" disabled>コメントを送信する</button>
          @else
            <textarea id="content" name="content" class="pd__textarea" rows="6" maxlength="255" placeholder="コメントを入力">{{ old('content') }}</textarea>
            <button type="submit" class="pd__submit">コメントを送信する</button>
          @endguest
        </form>
      </section>
    </div>
  </div>
</section>
@endsection