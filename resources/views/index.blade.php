@extends('layouts.app')

@section('content')
    <h2>Productos</h2>

    <hr>

    <div class="row">
        @forelse($products as $product)
            <div class="card col-sm-4">
                <a href="{{route('products.show',['product'=>$product->id])}}" onclick="showProduct(event, this)">
                    <img src="{{Storage::disk('products')->url($product->image)}}" class="card-img-top"
                         alt="{{$product->name}}">
                </a>
                <div class="card-body">
                    <h5 class="card-title">{{$product->name}}</h5>
                    <p class="card-text">{{$product->description}}</p>
                    <p class="card-text">${{number_format($product->price,2)}}</p>
                    <button class="btn btn-primary">Comprar</button>
                </div>
            </div>
        @empty
            <i>No hay productos</i>
        @endforelse
        <div class="pagination">
            {{$products->links()}}
        </div>
    </div>
@endsection
