<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ImportProductsFromApi;use Tests\TestCase;

class ApiExternalProcuctsTest extends TestCase
{
    use RefreshDatabase;
    private string $pathAPI;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pathAPI = 'https://fakestoreapi.com';
    }

    /*public function test_api_products_status(): void
    {
        $response = Http::get($this->pathAPI .  '/products');
        $this->assertEquals(200, $response->status());
    }*/

    /*public function test_import_products_from_api(): void
    {
        Queue::fake();

        $response = Http::get($this->pathAPI .  '/products');
        $this->assertEquals(200, $response->status());

        $this->artisan('products:import')
             ->expectsOutput('Importando produtos...')
             ->assertExitCode(0);

        Queue::assertPushed(ImportProductsFromApi::class);
    }*/

    public function test_api_products_status(): void
    {
        Http::fake([
            $this->pathAPI . '/products' => Http::response([
                ['id' => 1, 'title' => 'Test Product', 'price' => 10.99],
            ], 200),
        ]);

        $response = Http::withHeaders([
            'User-Agent' => 'LaravelTestBot/1.0',
            'Accept' => 'application/json',
        ])->get($this->pathAPI . '/products');

        $this->assertEquals(200, $response->status());
    }

    public function test_import_products_from_api(): void
    {
        Queue::fake();

        Http::fake([
            $this->pathAPI . '/products' => Http::response([
                ['id' => 1, 'title' => 'Test Product', 'price' => 10.99],
            ], 200),
        ]);

        $response = Http::withHeaders([
            'User-Agent' => 'LaravelTestBot/1.0',
            'Accept' => 'application/json',
        ])->get($this->pathAPI . '/products');

        $this->assertEquals(200, $response->status());

        $this->artisan('products:import')
             ->expectsOutput('Importando produtos...')
             ->assertExitCode(0);

        Queue::assertPushed(ImportProductsFromApi::class);
    }
}
