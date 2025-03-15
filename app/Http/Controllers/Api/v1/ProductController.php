<?php

namespace App\Http\Controllers\Api\v1;

use Throwable;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        // dd($request->all());

        // if () {

        // }
        return ProductResource::collection(Product::all());
    }

    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $product = Product::create($request->validated());

            return response()->json([
                'message' => 'Produto cadastrado com sucesso.',
                'product' => new ProductResource($product),
            ], 201);

        } catch (Throwable $th) {
            Log::error('Erro ao criar produto: ' . $th->getMessage(), ['exception' => $th]);
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): ProductResource
    {
        return new ProductResource(Product::findOrFail($id));
    }

    public function update(ProductUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->update($request->only([
                'name',
                'price',
                'description',
                'category',
                'image_url',
            ]));

            $product->refresh();

            return response()->json([
                'message' => 'Produto editado com sucesso.',
                'product' => new ProductResource($product),
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Produto não encontrado.'
            ], 404);

        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {

        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'message' => 'Produto deletado com sucesso.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Produto não encontrado.'
            ], 404);
        }
    }
    
    public function getProductsByCategory(string $category): ResourceCollection
    {
        $products = Product::where('category', $category)->get();
        return ProductResource::collection($products);
    }
    
    public function getProductsWithOrWithoutImage()
    {

    }
}
