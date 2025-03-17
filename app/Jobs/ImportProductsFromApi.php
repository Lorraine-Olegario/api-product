<?php

namespace App\Jobs;

use App\DTO\ProductDTO;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\ConsumerProductsAPI;
use Illuminate\Support\Facades\Log;

class ImportProductsFromApi implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        private ?string $id
    ) { }

    public function handle(ConsumerProductsAPI $api): void
    {
        try {        
            $api->consume($this->id);

        } catch (\Throwable $th) {
            Log::error("Erro ao importar produtos: " . $th->getMessage());
            throw $th;
        }
    }
}
