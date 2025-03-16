<?php

namespace App\Services;

use App\DTO\ProductDTO;
use GuzzleHttp\Client;
use Throwable;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class ConsumerProductsAPI
{
    private $client;
    private $pathAPI;

    public function __construct() {
        $this->client = new Client([
            'verify' => storage_path('cacert.pem'),
        ]);

        //!mudar nome variavel .env
        $this->pathAPI = env('API_ENDPOINT');
    }

    public function consume(string|null $id): void
    {
        try {

            $serachById = null;
            if (is_string($id)) {
                $serachById = "/$id";
            }

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
            Log::error($th->getMessage());
            throw $th;
        }
    }

    private function processProducts(array $data): void
    {
        if (!$this->validateProductData($data)) {
            Log::warning('Importação do produto inválidos', $data);
            return;
        }

        $productDTO = new ProductDTO(
            $data['title'],
            $data['price'],
            $data['description'],
            $data['category'],
            $data['image'],
        );
        
        $repository = new ProductRepository();
        $repository->updateAndInsert($productDTO);
    }

    private function validateProductData(array $data): bool
    {
        return isset($data['title']) && 
               isset($data['price']) && 
               is_numeric($data['price']) && 
               isset($data['description']) && 
               isset($data['category']);
    }
}
