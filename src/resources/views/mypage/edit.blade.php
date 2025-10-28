{{-- resources/views/mypage/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'プロフィール設定')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/mypage_edit.css') }}">
@endpush

@section('content')
<div class="pf-wrap">
  <h1 class="pf-title">プロフィール設定</h1>

  <form action="{{ route('mypage.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="pf-row">
      <div class="pf-left">
        <img
          id="pfAvatarPreview"
          class="pf-avatar"
          src="{{ $user->avatar_url ?: 'https://placehold.co/180x180?text=Avatar' }}"
          alt="avatar">

        <div class="pf-filebar">
          <label class="pf-filebtn" for="avatar">画像を選択する</label>
          <input id="avatar" name="avatar" type="file" accept="image/*" style="display:none">
        </div>
        <div id="pfFileName" class="pf-filename"></div>
        @error('avatar')<div class="pf-error">{{ $message }}</div>@enderror
      </div>

      <div class="pf-col">
        <div class="pf-field">
          <label for="name" class="pf-label">ユーザー名</label>
          <input id="name" type="text" name="name" class="pf-input"
                 value="{{ old('name', $user->name) }}">
          @error('name')<div class="pf-error">{{ $message }}</div>@enderror
        </div>

        <div class="pf-field">
          <label for="postal_code" class="pf-label">郵便番号</label>
          <input id="postal_code" type="text" name="postal_code" class="pf-input"
                 value="{{ old('postal_code', $user->postal_code) }}" placeholder="例) 123-4567">
          @error('postal_code')<div class="pf-error">{{ $message }}</div>@enderror
        </div>

        <div class="pf-field">
          <label for="address" class="pf-label">住所</label>
          <input id="address" type="text" name="address" class="pf-input"
                 value="{{ old('address', $user->address) }}" placeholder="市区町村・番地など">
          @error('address')<div class="pf-error">{{ $message }}</div>@enderror
        </div>

        <div class="pf-field">
          <label for="building" class="pf-label">建物名</label>
          <input id="building" type="text" name="building" class="pf-input"
                 value="{{ old('building', $user->building) }}" placeholder="建物名・号室（任意）">
          @error('building')<div class="pf-error">{{ $message }}</div>@enderror
        </div>

        <div class="pf-actions">
          <button class="pf-submit" type="submit">更新する</button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  const pfInput = document.getElementById('avatar');
  const pfPreview = document.getElementById('pfAvatarPreview');
  const pfName = document.getElementById('pfFileName');
  if (pfInput) {
    pfInput.addEventListener('change', (e) => {
      const f = e.target.files && e.target.files[0];
      if (!f) return;
      pfName.textContent = f.name;
      pfPreview.src = URL.createObjectURL(f);
    });
  }
</script>
@endsection