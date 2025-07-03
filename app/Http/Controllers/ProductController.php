<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => 'sometimes|uuid|exists:categories,id'
        ]);
        
        $query = Product::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        $products = $query->get();

        return response()->json($products);
    }

    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('products', 'public');

            $url = Storage::url($path);

            $validatedData['banner'] = $url;
        }

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    public function show(Product $product):JsonResponse
    {
        $product->load('category');
        return response()->json($product);   
    }

    public function update(UpdateProductRequest $request, Product $product):JsonResponse
    {
        $validatedData = $request->validated();

        if ($request->hasFile('banner')) {
            if ($product->banner) {
                $oldPath = str_replace(Storage::url(''), '', $product->banner);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('banner')->store('products', 'public');
            $validatedData['banner'] = Storage::url($path);
        }

        $product->update($validatedData);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
         $product->delete();

        return response()->json(['message' => 'Produto deletado com sucesso.']);
    }
}
