<div class="card">
    <img src="{{Storage::disk('products')->url($product->image)}}" class="card-img-top"
         alt="{{$product->name}}">
    <div class="card-body">
        <h5 class="card-title">{{$product->name}}</h5>
        <p class="card-text">{{$product->description}}</p>
        <p class="card-text">${{number_format($product->price,2)}}</p>
    </div>
</div>
