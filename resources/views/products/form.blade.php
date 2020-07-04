@php
    /**@var \App\Models\Product $product*/
    $creating = !$product->exists;
@endphp

@extends('layouts.app')

@section('content')
    <h2>{{__($creating ? 'Crear producto' : ('Editando ' . $product->name))}}</h2>

    <hr>

    <form action="{{$creating ? route('products.store') : route('products.update',['product'=>$product->id])}}"
          method="post" enctype="multipart/form-data">
        @csrf
        @if(!$creating)
            @method('PATCH')
        @endif
        <div class="form-group">
            <label>Nombre</label>
            <input name="name" type="text" class="form-control" value="{{$product->name}}" required/>
        </div>
        <div class="form-group">
            <label>Descripción</label>
            <textarea name="description" class="form-control" required>{{$product->description}}</textarea>
        </div>
        <div class="form-group">
            <label>Precio</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                </div>
                <input name="price" type="number" class="form-control" value="{{$product->price}}" min="1"
                       max="9999999999" required/>
            </div>
        </div>
        <div class="form-group">
            <label>Imagen</label>
            <input name="image" type="file" class="form-control" {!!$creating ? 'required' : ''!!}/>
            {{$product->image}}
        </div>

        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
@endsection
