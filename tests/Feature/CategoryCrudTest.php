<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test; 
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function um_usuario_autenticado_pode_criar_uma_categoria(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/categories', [
            'name' => 'Pizzas Especiais'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'name' => 'Pizzas Especiais'
                 ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Pizzas Especiais'
        ]);
    }

    #[Test]
    public function um_usuario_nao_autenticado_nao_pode_criar_uma_categoria(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Pizzas de Invasores'
        ]);

        $response->assertStatus(401);
        
        $this->assertDatabaseMissing('categories', [
            'name' => 'Pizzas de Invasores'
        ]);
    }

    #[Test]
    public function criacao_de_categoria_falha_com_nome_vazio(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/categories', [
            'name' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function um_usuario_autenticado_pode_listar_categorias(): void
    {
        $user = User::factory()->create();
        Category::factory()->count(3)->create();
        $specificCategory = Category::factory()->create(['name' => 'Bebidas']);

        $response = $this->actingAs($user)->getJson('/api/categories');

        $response->assertStatus(200);
        $response->assertJsonCount(4);
        $response->assertJsonFragment(['name' => 'Bebidas']);
    }

    #[Test]
    public function um_usuario_autenticado_pode_ver_uma_categoria_especifica(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    #[Test]
    public function um_usuario_autenticado_pode_atualizar_uma_categoria(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Nome Antigo']);

        $response = $this->actingAs($user)->putJson("/api/categories/{$category->id}", [
            'name' => 'Nome Novo e Atualizado'
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Nome Novo e Atualizado']);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Nome Novo e Atualizado'
        ]);
    }

    #[Test]
    public function um_usuario_autenticado_pode_deletar_uma_categoria_vazia(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/categories/{$category->id}");

        $response->assertSuccessful(); 
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    #[Test]
    public function nao_pode_deletar_categoria_com_produtos_associados(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Product::factory()->for($category)->create();

        $response = $this->actingAs($user)->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(409);
        $response->assertJsonFragment(['error' => 'Não é possível deletar a categoria, pois ela possui produtos associados.']);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}