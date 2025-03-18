<?php

namespace App\Services;

use App\DTO\ProductDTO;
use GuzzleHttp\ClientInterface;
use Throwable;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConsumerProductsAPI
{
    private $pathAPI;

    public function __construct(
        private ClientInterface $client,
        private ProductRepository $repository
    ){
        $this->pathAPI = env('API_PRODUCTS');
    }

    public function consume(string|null $id): void
    {
        try {

            $serachById = is_string($id) ? "/$id" : null;
            $res = $this->client->request('GET', $this->pathAPI . 'products' . $serachById, [
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]);

            $data = json_decode($res->getBody()->getContents(), true);

            if (is_string($id)) {
                $this->processProducts($data);
            } else {
                foreach ($data as $product) {
                    $this->processProducts($product);
                }
            }
            
        } catch (Throwable $th) {
            Log::error("Erro ao consumir API: " . $th->getMessage());
            throw $th;
        }
    }

    private function processProducts(array $data): void
    {
        if (!$this->validateProductData($data)) {
            Log::warning('Importação de produto inválidos', $data);
            return;
        }

        $productDTO = new ProductDTO(
            $data['title'],
            $data['price'],
            $data['description'],
            $data['category'],
            $data['image'],
        );

        $this->repository->updateAndInsert($productDTO);
    }

    private function validateProductData(array $data): bool
    {
        $validator = Validator::make($data, [
            'title'       => 'required|string',
            'price'       => 'required|numeric|min:0',
            'description' => 'required|string',
            'category'    => 'required|string',
            'image'       => 'nullable|string',
        ]);
    
        if ($validator->fails()) {
            Log::warning('Produto inválido', $validator->errors()->toArray());
            return false;
        }
    
        return true;
    }
}
