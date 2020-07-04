@extends('layouts.app')

@section('content')
    @include('products.partial_show',['product'=>$product])
@endsection
