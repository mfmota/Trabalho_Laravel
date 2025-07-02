<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => 'sometimes|uuid|exists:categories,id'
        ]);
        
        // Inicia a query builder
        $query = Product::query();

        // Se o parâmetro 'category_id' estiver presente na URL...
        if ($request->has('category_id')) {
            // ...adiciona um 'where' na query para filtrar.
            $query->where('category_id', $request->query('category_id'));
        }

        // Executa a query e busca os produtos
        $products = $query->get();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('banner')) {
            // 3. Salva o arquivo e pega o caminho
            // O arquivo será salvo em 'storage/app/public/products'
            $path = $request->file('banner')->store('products', 'public');

            // 4. Constrói a URL completa para o arquivo
            $url = Storage::url($path);

            // 5. Atualiza o campo 'banner' com a URL para salvar no banco
            $validatedData['banner'] = $url;
        }

        $product = Product::create($validatedData);

        // Retorna o produto criado com status 201
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
