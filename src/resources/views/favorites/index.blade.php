@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@section('content')

    <div class="products-grid">
        @foreach($items as $it)
            <a href="{{ url('/item/'.$it->id) }}" class="item-card">
                <div class="item-card__thumb" style="aspect-ratio:1/1;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
                    <img src="{{ $it->img_url }}" alt="{{ $it->title }}" class="item-card__img" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div class="item-card__title" style="margin-top:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $it->title }}
                </div>
            </a>
        @endforeach
    </div>

    <div class="pagination">
        {{ $items->links() }}
    </div>
@endsection