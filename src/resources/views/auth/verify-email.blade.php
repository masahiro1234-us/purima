@extends('layouts.auth')

@section('title','メール認証 | Prima')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endpush

@section('content')
<section class="verify__wrap">
  <p class="verify__lead">
    <span class="verify__line">登録していただいたメールアドレスに認証メールを送付しました。</span>
    <span class="verify__line">メール認証を完了してください。</span>
  </p>

  <a href="#" class="verify__primary">認証はこちらから</a>

  <form method="post" action="{{ route('verification.send') }}" class="verify__resend">
    @csrf
    <button type="submit" class="verify__link">認証メールを再送する</button>
  </form>
</section>
@endsection