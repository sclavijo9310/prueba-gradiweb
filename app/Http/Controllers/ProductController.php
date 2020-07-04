<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::query()->paginate(5);

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Product();

        return view('products.form', compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data = $request->except('image');

        $data['image'] = $request->file('image')->store('images', 'products');

        /**@var Product $product */
        $product = Product::query()->create($data);

        if ($product)
            return redirect()->route('products.index');
        else abort(500, 'Ha ocurrido un error interno creando el producto');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('products.form', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if (Storage::disk('products')->exists($product->image))
                Storage::disk('products')->delete($product->image);
            $data['image'] = $request->file('image')->store('images', 'products');
        }

        if ($product->update($data))
            return redirect()->route('products.index');
        else abort(500, 'Ha ocurrido un error interno actualizando el producto');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        if (Storage::disk('products')->exists($product->image))
            Storage::disk('products')->delete($product->image);

        if ($product->delete())
            return redirect()->route('products.index');
        else abort(500, 'Ha ocurrido un error interno eliminando el producto');

    }
}
