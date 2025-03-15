<?php

namespace App\Console\Commands;

use App\Services\ConsumerProductsAPI;
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

            $api = new ConsumerProductsAPI();
            $api->consume($id);

            if ($id) {
                echo "Importando produtos com ID: " . $id . ".";
            } else {
                echo "Importando produtos.";
            }

        } catch (Throwable $th) {
            echo 'Erro na importação: ' . $th->getMessage();
        }
    }
}
