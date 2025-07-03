<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_fluxo_completo_de_um_pedido_funciona_corretamente(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $productA = Product::factory()->for($category)->create(['name' => 'Pizza Calabresa', 'price' => 45.00]);
        $productB = Product::factory()->for($category)->create(['name' => 'Refrigerante', 'price' => 10.00]);
        
        // CRIAR UM NOVO PEDIDO (EM MODO RASCUNHO)
        $response = $this->actingAs($user)->postJson('/api/orders', [
            'table' => 10,
            'name' => 'Cliente da Mesa 10'
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['table' => 10, 'draft' => true, 'status' => false]);
        $this->assertDatabaseHas('orders', ['table' => 10]);
        
        $orderId = $response->json('id');

        // ADICIONAR O PRIMEIRO ITEM AO PEDIDO
        $response = $this->actingAs($user)->postJson("/api/orders/{$orderId}/items", [
            'product_id' => $productA->id,
            'amount' => 2, 
        ]);
 
        $response->assertStatus(201);
        $this->assertDatabaseHas('items', [
            'order_id' => $orderId,
            'product_id' => $productA->id,
            'amount' => 2
        ]);
        
        $itemA_id = $response->json('id');

        //ADICIONAR O SEGUNDO ITEM AO PEDIDO
        $response = $this->actingAs($user)->postJson("/api/orders/{$orderId}/items", [
            'product_id' => $productB->id,
            'amount' => 3, 
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseCount('items', 2); 

        // REMOVER O PRIMEIRO ITEM DO PEDIDO
        $response = $this->actingAs($user)->deleteJson("/api/orders/{$orderId}/items/{$itemA_id}");

        $response->assertSuccessful();
        $this->assertDatabaseMissing('items', ['id' => $itemA_id]); 
        $this->assertDatabaseCount('items', 1); 

        // ENVIAR O PEDIDO (TIRAR DO MODO RASCUNHO)
        $response = $this->actingAs($user)->putJson("/api/orders/{$orderId}/send");

        $response->assertStatus(200);
        $response->assertJsonFragment(['draft' => false]);
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'draft' => false]);


        // FINALIZAR O PEDIDO (MARCAR COMO CONCLUÍDO)
        $response = $this->actingAs($user)->putJson("/api/orders/{$orderId}/finish");

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => true]);
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'status' => true]);
    }
}