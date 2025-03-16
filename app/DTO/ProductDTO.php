<?php

namespace App\DTO;

class ProductDTO
{
    public function __construct(
        public string $name,
        public float $price,
        public string $description,
        public string $category,
        public string $image_url,
    ) { }
}
