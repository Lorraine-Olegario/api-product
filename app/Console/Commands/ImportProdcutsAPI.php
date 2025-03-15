<?php

namespace App\Console\Commands;

use App\Jobs\ImportProductsFromApi;
use Illuminate\Console\Command;
use Throwable;

class ImportProdcutsAPI extends Command
{
    protected $signature = 'products:import {--id=}';
    protected $description = 'Command description';

    public function handle()
    {
        try {            
            $id = $this->option('id');
            ImportProductsFromApi::dispatch($id);

            if ($id) {
                $this->info("Importando produtos com ID: $id");
            } else {
                $this->info("Importando produtos...");
            }

        } catch (Throwable $th) {
            $this->error('Erro na importação: ' . $th->getMessage());
        }
    }
}
