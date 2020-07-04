@extends('layouts.app')

@section('content')
    <h2>Productos</h2>

    <a href="{{route('products.create')}}" class="btn btn-primary btn-lg" role="button"><i class="fa fa-plus"></i> Crear</a>

    <hr>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Imagen</th>
                <th scope="col">Nombre</th>
                <th scope="col">Description</th>
                <th scope="col">Precio</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <th scope="row">{{$product->id}}</th>
                    <th>
                        <img src="{{Storage::disk('products')->url($product->image)}}" alt="{{$product->name}}"
                             class="img-thumbnail img-fluid" style="max-width: 200px"></th>
                    <td>{{$product->name}}</td>
                    <td>{{$product->description}}</td>
                    <td>${{number_format($product->price,2)}}</td>
                    <td>
                        <a href="{{route('products.show',['product'=>$product->id])}}"
                           onclick="showProduct(event, this)" class="btn btn-sm" role="button"
                           title="Ver producto"><i
                                class="fa fa-eye"></i></a>
                        <a href="{{route('products.edit',['product'=>$product->id])}}" class="btn btn-sm" role="button"
                           title="Editar producto"><i
                                class="fa fa-pencil"></i></a>
                        <a href="{{route('products.destroy',['product'=>$product->id])}}" class="btn btn-sm"
                           role="button" title="Eliminar producto" onclick="deleteProduct(event, this)"><i
                                class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6"><i>No hay productos</i></td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {{$products->links()}}
    </div>
    <form id="destroy_frm" action="" method="post">
        @csrf
        @method('DELETE')
    </form>
    <script type="text/javascript">
        let destroy_frm = document.getElementById('destroy_frm');

        function deleteProduct(event, elem) {
            event.preventDefault();
            if (confirm('¿Está seguro de eliminar este producto?')) {
                destroy_frm.action = elem.href;
                destroy_frm.submit();
            }
        }
    </script>
@endsection
