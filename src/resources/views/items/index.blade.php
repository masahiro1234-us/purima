@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endpush

@section('content')
<div class="products-grid">
    @foreach($items as $it)
        @php $sold = !is_null($it->buyer_id ?? null); @endphp

        <a href="{{ url('/item/'.$it->id) }}" class="item-card {{ $sold ? 'card--sold' : '' }}">
            <div class="card__thumb"
                 style="aspect-ratio:1/1;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
                <img
                    src="{{ $it->img_url }}"
                    alt="{{ $it->title }}"
                    class="item-card__img"
                    style="width:100%;height:100%;object-fit:cover;"
                >
                @if($sold)
                    <span class="wm-sold">SOLD</span>
                @endif
            </div>
            <div class="item-card__title">
                {{ $it->title }}
            </div>
        </a>
    @endforeach
</div>

<div class="pagination">
    {{ $items->links() }}
</div>
@endsection