{{-- resources/views/sell/create.blade.php --}}
@extends('layouts.app')

@section('title','商品の出品')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endpush

@section('content')
<section class="sell">
  <h1 class="sell__title">商品の出品</h1>

  <form class="sell__form" action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- 画像 --}}
    <div class="sell__block">
      <div class="sell__label">商品画像</div>

      <div class="sell__drop">
        <label class="sell__filebtn">
          画像を選択する
          {{-- ← JS から拾えるように id を付与 --}}
          <input id="imageInput" type="file" name="image" accept="image/*" hidden>
        </label>

        {{-- ← ボタンのすぐ下にファイル名を出す場所 --}}
        <div id="selectedFileName" class="sell__filename"></div>
      </div>

      @error('image')
        <p class="sell__error">{{ $message }}</p>
      @enderror
    </div>

    {{-- 詳細 見出しライン --}}
    <div class="sell__subhead">商品の詳細</div>

    {{-- カテゴリー（タグ風） --}}
    <div class="sell__block">
      <div class="sell__label">カテゴリー</div>

      <div class="sell__chips">
        @php
          $tags = [
            'ファッション','家具','インテリア','レディース','メンズ','コスメ',
            '車','ゲーム','スポーツ','キッチン','ハンドメイド','アクセサリー',
            'おもちゃ','ベビー・キッズ'
          ];
          $oldCats = collect(old('categories', []));
        @endphp

        @foreach($tags as $t)
          <label class="chip">
            <input type="checkbox" name="categories[]" value="{{ $t }}" {{ $oldCats->contains($t) ? 'checked' : '' }} hidden>
            <span>{{ $t }}</span>
          </label>
        @endforeach
      </div>
    </div>

    {{-- 状態 --}}
    <div class="sell__block">
      <label class="sell__label" for="status">商品の状態</label>
      <select id="status" name="status" class="sell__select">
        <option value="" disabled {{ old('status') ? '' : 'selected' }}>選択してください</option>
        <option value="新品"                 {{ old('status')==='新品' ? 'selected' : '' }}>新品</option>
        <option value="目立った傷や汚れなし" {{ old('status')==='目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
        <option value="やや傷や汚れあり"     {{ old('status')==='やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
        <option value="状態が悪い"           {{ old('status')==='状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
        <option value="付属品/箱 なし"      {{ old('status')==='付属品/箱 なし' ? 'selected' : '' }}>付属品/箱 なし</option>
      </select>
      @error('status')
        <p class="sell__error">{{ $message }}</p>
      @enderror
    </div>

    {{-- 商品名 & 説明（幅を統一） --}}
    <div class="sell__block">
      <div class="sell__label">商品名と説明</div>

      <label class="sell__field">
        <span>商品名</span>
        <input type="text" name="title" value="{{ old('title') }}">
      </label>
      @error('title')<p class="sell__error">{{ $message }}</p>@enderror

      <label class="sell__field">
        <span>ブランド名</span>
        <input type="text" name="brand" value="{{ old('brand') }}">
      </label>

      <label class="sell__field">
        <span>商品の説明</span>
        <textarea name="description" rows="4">{{ old('description') }}</textarea>
      </label>
    </div>

    {{-- 価格 --}}
    <div class="sell__block">
      <label class="sell__label" for="price">販売価格</label>
      <div class="sell__price">
        <span>¥</span>
        <input id="price" type="number" name="price" value="{{ old('price') }}" min="1">
      </div>
      @error('price')<p class="sell__error">{{ $message }}</p>@enderror
    </div>

    {{-- 送信 --}}
    <div class="sell__actions">
      <button class="sell__submit" type="submit">出品する</button>
    </div>
  </form>
</section>

{{-- ビュー内に素直に置く（@stack 依存なし） --}}
<script>
  (function () {
    var input = document.getElementById('imageInput');
    var nameBox = document.getElementById('selectedFileName');
    if (!input || !nameBox) return;

    input.addEventListener('change', function () {
      if (this.files && this.files.length > 0) {
        nameBox.textContent = this.files[0].name;
      } else {
        nameBox.textContent = '';
      }
    });
  })();
</script>
@endsection