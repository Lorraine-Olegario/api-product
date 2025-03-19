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
        $this->pathAPI = env('API_PRODUCTS', 'https://fakestoreapi.com'); // Garanta o valor padrão
    }

    public function test_api_products_status(): void
    {
        $response = Http::get($this->pathAPI .  '/products');
        $this->assertEquals(200, $response->status());
    }

    public function test_import_products_from_api(): void
    {
        Queue::fake();

        $response = Http::get($this->pathAPI .  '/products');
        $this->assertEquals(200, $response->status());

        $this->artisan('products:import')
             ->expectsOutput('Importando produtos...')
             ->assertExitCode(0);

        $job = new ImportProductsFromApi(null);
        $this->app->call([$job, 'handle']);
    }
}
