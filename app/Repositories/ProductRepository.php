<?php

namespace App\Repositories;

use App\DTO\ProductDTO;
use App\Models\Product;

class ProductRepository
{
    public function updateAndInsert(ProductDTO $productDTO)
    {
        return Product::updateOrCreate(
            ['name' => $productDTO->name],
            [
                'price' => $productDTO->price,
                'description' => $productDTO->description,
                'category' => $productDTO->category,
                'image_url' => $productDTO->image_url
            ]
        );
    }
}