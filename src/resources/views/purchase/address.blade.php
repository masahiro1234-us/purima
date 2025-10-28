{{-- resources/views/purchase/address.blade.php --}}
@extends('layouts.app')
@section('title','住所の変更')

@push('styles')
  {{-- purchase.css を外すならこちらだけでOK。併用しても可 --}}
  <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endpush

@section('content')
<section class="address-edit">
  <h1 class="address-edit__title">送付先住所変更画面</h1>

  <form class="address-edit__form" method="post" action="{{ route('purchase.address.update', $item) }}">
    @csrf

    <label class="address-edit__field">
      <span>郵便番号</span>
      <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
      @error('postal_code')<p class="form-error">{{ $message }}</p>@enderror
    </label>

    <label class="address-edit__field">
      <span>住所</span>
      <input type="text" name="address" value="{{ old('address', $user->address) }}">
      @error('address')<p class="form-error">{{ $message }}</p>@enderror
    </label>

    <label class="address-edit__field">
      <span>建物名</span>
      <input type="text" name="building" value="{{ old('building', $user->building) }}">
      @error('building')<p class="form-error">{{ $message }}</p>@enderror
    </label>

    <button class="address-edit__submit" type="submit">更新する</button>
  </form>
</section>
@endsection