<?php

namespace Tests\Feature\Relationships;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OneToManyRelationshipTest extends TestCase
{
    use RefreshDatabase; 

    //==============================================================
    //== Testes para o relacionamento: Category <--> Product
    //==============================================================

    /** @test */
    public function uma_categoria_pode_ter_muitos_produtos(): void
    {
        // 1. Cenário (Given): Criamos uma Categoria e 3 Produtos para ela.
        $category = Category::factory()->create();
        Product::factory()->count(3)->for($category)->create();

        // 2. Ação (When): Acessamos a propriedade de relacionamento 'products'.
        $productsFromCategory = $category->products;

        // 3. Afirmação (Then): Verificamos se os resultados estão corretos.
        $this->assertCount(3, $productsFromCategory);
        $this->assertInstanceOf(Product::class, $productsFromCategory->first());
    }

    /** @test */
    public function um_produto_pertence_a_uma_categoria(): void
    {
        // 1. Cenário: Criamos uma Categoria e um Produto para ela.
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        // 2. Ação: Acessamos a propriedade de relacionamento 'category'.
        $categoryFromProduct = $product->category;

        // 3. Afirmação: Verificamos se o produto está corretamente associado à categoria.
        $this->assertInstanceOf(Category::class, $categoryFromProduct);
        $this->assertEquals($category->id, $categoryFromProduct->id);
    }

    //==============================================================
    //== Testes para o relacionamento: Order <--> Item
    //==============================================================

    /** @test */
    public function um_pedido_pode_ter_muitos_itens(): void
    {
        // 1. Cenário: Criamos um Pedido (Order) e 5 Itens para ele.
        $order = Order::factory()->create();
        Item::factory()->count(5)->for($order)->create();

        // 2. Ação: Acessamos a relação 'items' do pedido.
        $itemsFromOrder = $order->items;

        // 3. Afirmação: Verificamos se o pedido contém os 5 itens.
        $this->assertCount(5, $itemsFromOrder);
        $this->assertInstanceOf(Item::class, $itemsFromOrder->first());
    }

    /** @test */
    public function um_item_pertence_a_um_pedido(): void
    {
        // 1. Cenário: Criamos um Pedido e um Item para ele.
        $order = Order::factory()->create();
        $item = Item::factory()->for($order)->create();

        // 2. Ação: Acessamos a relação 'order' do item.
        $orderFromItem = $item->order;

        // 3. Afirmação: Verificamos a associação correta.
        $this->assertInstanceOf(Order::class, $orderFromItem);
        $this->assertEquals($order->id, $orderFromItem->id);
    }
    
    //==============================================================
    //== Testes para o relacionamento: Product <--> Item
    //==============================================================

    /** @test */
    public function um_produto_pode_estar_em_muitos_itens(): void
    {
        // 1. Cenário: Criamos um Produto. Depois criamos 4 Itens diferentes para esse mesmo produto.
        // Isso simula o mesmo produto sendo vendido em 4 pedidos diferentes, por exemplo.
        $product = Product::factory()->create();
        Item::factory()->count(4)->for($product)->create();

        // 2. Ação: Acessamos a relação 'items' do produto.
        $itemsFromProduct = $product->items;

        // 3. Afirmação: Verificamos se o produto está associado aos 4 itens.
        $this->assertCount(4, $itemsFromProduct);
        $this->assertInstanceOf(Item::class, $itemsFromProduct->first());
    }

    /** @test */
    public function um_item_pertence_a_um_produto(): void
    {
        // 1. Cenário: Criamos um Produto e um Item para ele.
        $product = Product::factory()->create();
        $item = Item::factory()->for($product)->create();

        // 2. Ação: Acessamos a relação 'product' do item.
        $productFromItem = $item->product;
        
        // 3. Afirmação: Verificamos a associação.
        $this->assertInstanceOf(Product::class, $productFromItem);
        $this->assertEquals($product->id, $productFromItem->id);
    }
}