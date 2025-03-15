<?php

namespace App\Repositories;

use App\Models\Product;

//!Mudar o nome do Repositorio apenas para API 
class ProductRepository
{
    public function save(array $data): void
    {
        //!não pode cadastrar mesmo nome
        //!os dados que não forem importado por erro
        $products = $this->validate($data);

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['title']],
                [
                    'price' => $product['price'],
                    'description' => $product['description'],
                    'category' => $product['category'],
                    'image_url' => $product['image'],
                ]
            );
        }
    }

    public function saveSingleItem(array $data): void
    {
        $product = $this->validate($data);
        Product::updateOrCreate(
            ['name' => $product['title']],
            [
                'price' => $product['price'],
                'description' => $product['description'],
                'category' => $product['category'],
                'image_url' => $product['image'],
            ]
        );
    }

    //!Não é responsabilidade do repositorio validar dados
    //!validar os dados como price
    private function validate(array $data)
    {
        $requiredFields = ['title', 'price', 'description', 'category', 'image'];
        
        foreach ($requiredFields as $field) {
            if (!isset($field)) {
                throw new \Exception("Campo '$field' é obrigatório.");
            }
        }

        return $data;
    }
}