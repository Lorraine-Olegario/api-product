<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiProcuctsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_products(): void
    {       
        $response = $this->getJson('api/v1/products');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
            "data" => [
                '*' => [
                    "name",
                    "price",
                    "description",
                    "category",
                    "image_url",
                    "created_at",
                    "updated_at",
                ]
            ]
        ]);
    }

    public function test_get_product_by_id(): void
    {       
        $product = Product::factory()->create();
        $response = $this->getJson("api/v1/products/{$product->id}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
            "data" => [
                "name",
                "price",
                "description",
                "category",
                "image_url",
                "created_at",
                "updated_at",
            ]
        ]);
    }

    public function test_delete_product(): void
    {       
        $product = Product::factory()->create();
        $response = $this->deleteJson("api/v1/products/{$product->id}");
        
        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_update_product_category(): void
    {       
        $product = Product::factory()->create();
        $response = $this->patchJson("api/v1/products/{$product->id}", [
            'category' => 'livro',
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category' => 'livro',
        ]);
    }

    public function test_create_product(): void
    {       
        $data = [
            'name' => 'Harry Potter',
            'price' => '29.99',
            'description' => 'Book Harry Potter',
            'category' => 'Book',
            'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRi8dB5IiOPLqRNFrEecyuM4bI0PeuOQ3MpGg&s',
        ];

        $response = $this->postJson("api/v1/products", $data);
        
        $response->assertStatus(201)
            ->assertJson([
                "message" => "Produto cadastrado com sucesso.",
            ]);

        $this->assertDatabaseHas('products', $data);
    }

    public function test_filter_products_by_category(): void
    {
        $category = 'book';
        Product::factory(3)->create([
            'category' => $category
        ]);

        $response = $this->getJson("api/v1/products?category=$category");
        
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
            "data" => [
                '*' => [
                    "name",
                    "price",
                    "description",
                    "category",
                    "image_url",
                    "created_at",
                    "updated_at",
                ]
            ]
        ]);
    }

    public function test_filter_products_by_name(): void
    {
        $name = 'Coleira p/ Gato';
        Product::factory()->create([
            'name' => $name
        ]);

        $response = $this->getJson("api/v1/products?name=$name");
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_filter_products_with_image(): void
    {
        $url = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRi8dB5IiOPLqRNFrEecyuM4bI0PeuOQ3MpGg&s';
        
        Product::factory(2)->create(['image_url' => $url]);
        Product::factory(5)->create(['image_url' => null]);

        $response = $this->getJson("api/v1/products?has_image=true");
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_create_product_duplicate_name_error(): void
    {
        $prod1 = Product::factory()->create([
            'name' => 'nome_teste'
        ]);

        $data = [
            'name' => 'nome_teste',
            'price' => '0.99',
            'description' => 'Esse é um teste de erro',
            'category' => 'teste',
        ];

        $response = $this->postJson("api/v1/products", $data);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_create_product_invalid_price_error(): void
    {
        $data = [
            'name' => 'nome_teste',
            'price' => 'o',
            'description' => 'Esse é um teste de erro para valor com string',
            'category' => 'teste',
        ];

        $response = $this->postJson("api/v1/products", $data);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }
}
