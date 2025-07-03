<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    #[Test]
    public function um_usuario_autenticado_pode_criar_um_produto_com_imagem(): void
    {
        Storage::fake('public');

        $productData = [
            'name' => 'Pizza Nova',
            'price' => 50.75,
            'description' => 'Descrição da pizza nova.',
            'category_id' => $this->category->id,
            'banner' => UploadedFile::fake()->image('banner.jpg', 100, 100) 
        ];

        $response = $this->actingAs($this->user)->postJson('/api/products', $productData);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Pizza Nova']);

        $this->assertDatabaseHas('products', ['name' => 'Pizza Nova']);

        $bannerPath = str_replace('/storage/', '', $response->json('banner'));
        Storage::disk('public')->assertExists($bannerPath);
    }

    #[Test]
    public function criacao_de_produto_falha_se_categoria_nao_existe(): void
    {
        $productData = Product::factory()->make(['category_id' => 'abc-123-def-456'])->toArray();

        $response = $this->actingAs($this->user)->postJson('/api/products', $productData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('category_id');
    }

    #[Test]
    public function um_usuario_autenticado_pode_listar_produtos(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    #[Test]
    public function pode_filtrar_produtos_por_categoria(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        Product::factory()->count(3)->for($categoryA)->create();
        Product::factory()->count(2)->for($categoryB)->create();

        $response = $this->actingAs($this->user)->getJson("/api/products?category_id={$categoryA->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        
        $this->assertEquals($categoryA->id, $response->json('0.category_id'));
    }

    #[Test]
    public function um_usuario_autenticado_pode_atualizar_um_produto(): void
    {
        $product = Product::factory()->for($this->category)->create();

        $updateData = ['name' => 'Nome do Produto Atualizado', 'price' => 99.99];

        $response = $this->actingAs($this->user)->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Nome do Produto Atualizado']);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Nome do Produto Atualizado',
            'price' => 99.99
        ]);
    }

    #[Test]
public function um_usuario_autenticado_pode_deletar_um_produto(): void
{
    Storage::fake('public');

    // Criamos o produto e o banner. O ->store() retorna o caminho relativo.
    $relativePath = UploadedFile::fake()->image('banner.jpg')->store('products', 'public');

    // No mundo real, salvamos a URL completa no banco.
    $product = Product::factory()->create(['banner' => Storage::url($relativePath)]);

    // Ação: Deleta o produto
    $response = $this->actingAs($this->user)->deleteJson("/api/products/{$product->id}");

    // Afirmação: Verifica o sucesso e a remoção do DB.
    $response->assertSuccessful();
    $this->assertDatabaseMissing('products', ['id' => $product->id]);

    // Afirmação Chave (CORRIGIDA): 
    // Usamos o caminho relativo para verificar se o arquivo foi deletado do disco.
    Storage::disk('public')->assertMissing($relativePath);
}
}