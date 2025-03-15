<?php

use App\Http\Controllers\Api\v1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('products', ProductController::class)->except(['create', 'edit']);
    Route::get('/products/category/{category_name}', [ProductController::class, 'getProductsByCategory']);
});

// Route::get('/products/images', [ProductController::class, 'getProductsWithOrWithoutImage']);
// /products?name={nome}&category={categoria} (busca geral)
// /products/category/{category_name} (filtro por categoria)
// /products?has_image={true|false} (filtro por imagem)