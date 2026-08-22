@extends('layouts.app')
@section('content')
<div class="row">
  <div class="col-md-6">
    @if($product->image)<img src="{{ asset('storage/'.$product->image) }}" class="img-fluid">@endif
  </div>
  <div class="col-md-6">
    <h2>{{ $product->name }}</h2>
    <p class="text-muted">{{ $product->category->name }} · {{ $product->material }}</p>
    <h4 style="color:var(--wood)">{{ number_format($product->price,0,',','.') }} ₫</h4>
    <p>{{ $product->description }}</p>
    <p>Tồn kho: {{ $product->stock }}</p>
    <form method="POST" action="{{ route('cart.add') }}">@csrf
      <input type="hidden" name="product_id" value="{{ $product->id }}">
      <input type="number" name="quantity" value="1" min="1" class="form-control w-25 d-inline">
      <button class="btn btn-primary">Thêm vào giỏ</button>
    </form>
  </div>
</div>
@endsection
