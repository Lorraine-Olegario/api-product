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
    protected $perPageMax = 50;

    /**
     * @group Listar todos os produtos
     *
     * Retorna uma lista paginada de produtos com filtros opcionais por nome e categoria.
     *
     * @queryParam perPage integer Quantidade de itens por página. Padrão: 50. Exemplo: 10
     * @queryParam name string Filtro por nome do produto (busca parcial). Exemplo: "Mens Casual"
     * @queryParam category string Filtro por categoria do produto (busca parcial). Exemplo: "men's clothing"
     * @response 200 {"data": [{"id": 1, "name": "Mens Casual Premium Slim Fit T-Shirts ", "price": "22.30", "description": "Slim-fitting style...", "category": "men's clothing", "image_url": "https:\/\/fakestoreapi.com\/img\/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg", "created_at": "1 day ago", "updated_at": "1 day ago"}], "meta": {"current_page": 1, "per_page": 50, "total": 1}}     
     * @response 500 {"error": "Erro ao listar produtos"}
     */
    public function index(Request $request): ResourceCollection|JsonResponse
    {
        try {
            $perPage = $request->input('perPage') ?? $this->perPageMax;
            $q = Product::query();

            if ($request->has('name')) {
                $q->where('name', 'like', '%' . $request->input('name') . '%');
            }

            if ($request->has('category')) {
                $q->where('category', 'like', '%' . $request->input('category') . '%');
            }

            $product = $q->paginate($perPage);
            return ProductResource::collection($product);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar produtos'], 500);
        }
    }

    /**
     * @group Criar um novo produto
     *
     * Cadastra um novo produto com os dados fornecidos.
     *
     * @bodyParam name string required Nome do produto. Exemplo: "Camiseta"
     * @bodyParam price float required Preço do produto. Exemplo: 29.90
     * @bodyParam description string Descrição do produto. Exemplo: "Camiseta de algodão"
     * @bodyParam category string required Categoria do produto. Exemplo: "Roupas"
     * @bodyParam image_url string URL da imagem do produto. Exemplo: "http://example.com/image.jpg"
     * @response 201 {"message": "Produto cadastrado com sucesso.", "product": {"id": 1, "name": "Mens Casual Premium Slim Fit T-Shirts ", "price": "22.30", "description": "Slim-fitting style...", "category": "men's clothing", "image_url": "https:\/\/fakestoreapi.com\/img\/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg", "created_at": "1 day ago", "updated_at": "1 day ago"}}     
     * @response 500 {"message": "Erro ao criar produto"}
     */
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

    /**
     * @group Exibir um produto específico
     *
     * Retorna os detalhes de um produto com base no ID fornecido.
     *
     * @urlParam id integer required ID do produto. Exemplo: 1
     * @response 200 {"id": 1, "name": "Mens Casual Premium Slim Fit T-Shirts ", "price": "22.30", "description": "Slim-fitting style...", "category": "men's clothing", "image_url": "https:\/\/fakestoreapi.com\/img\/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg", "created_at": "1 day ago", "updated_at": "1 day ago"}     
     * @response 404 {"message": "Produto não encontrado"}
     */
    public function show(int $id): ProductResource
    {
        return new ProductResource(Product::findOrFail($id));
    }

    /**
     * @group Atualizar um produto existente
     *
     * Atualiza os dados de um produto específico com base no ID.
     *
     * @urlParam id integer required ID do produto. Exemplo: 1
     * @bodyParam name string Nome do produto. Exemplo: "Camiseta Atualizada"
     * @bodyParam price float Preço do produto. Exemplo: 39.90
     * @bodyParam description string Descrição do produto. Exemplo: "Camiseta de algodão atualizada"
     * @bodyParam category string Categoria do produto. Exemplo: "Roupas"
     * @bodyParam image_url string URL da imagem do produto. Exemplo: "http://example.com/new-image.jpg"
     * @response 200 {"message": "Produto editado com sucesso.", "product": {"id": 1, "name": "Mens Casual Premium Slim Fit T-Shirts ", "price": "22.30", "description": "Slim-fitting style...", "category": "men's clothing", "image_url": "https:\/\/fakestoreapi.com\/img\/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg", "created_at": "1 day ago", "updated_at": "1 day ago"}}     
     * @response 404 {"message": "Produto não encontrado"}
     * @response 500 {"message": "Erro ao atualizar produto"}
     */
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

    /**
     * @group Deletar um produto
     *
     * Remove um produto específico com base no ID.
     *
     * @urlParam id integer required ID do produto. Exemplo: 1
     * @response 200 {"message": "Produto deletado com sucesso"}
     * @response 404 {"message": "Produto não encontrado"}
     */
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
}
