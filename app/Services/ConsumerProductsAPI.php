<?php

namespace App\Services;

use GuzzleHttp\Client;
use Throwable;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;

class ConsumerProductsAPI 
{    
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => storage_path('cacert.pem'),
        ]);
    }

    public function consume(string|null $id): void
    {
        try {

            $serachById= null;
            if (is_string($id)) {
                $serachById = "/$id";
            }

            //!mudar nome variavel .env
            $path = env('API_ENDPOINT');            
            $res = $this->client->request('GET', $path . 'products'.$serachById);
            $data = json_decode($res->getBody(), true);
    
            $repository = new ProductRepository();

            if (is_string($id)) {
                $repository->saveSingleItem($data);
            } else {
                $repository->save($data);
            }
    
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            throw $th;
        }
    }
}