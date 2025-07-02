<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddItemRequest;
use App\Models\Order;
use App\Models\Item;

class OrderItemController extends Controller
{
    // Adiciona um item a um pedido existente (substitui AddItemController)
    public function store(AddItemRequest $request, Order $order) //
    {
        // A validação já foi feita pelo AddItemRequest
        $data = $request->validated();

        $item = $order->items()->create([
            'product_id' => $data['product_id'],
            'amount' => $data['amount'],
        ]);

        return response()->json($item, 201);
    }

    // Remove um item de um pedido (substitui RemoveItemController)
    public function destroy(Order $order, Item $item) //
    {
        // Validação extra para garantir que o item pertence ao pedido fornecido
        if ($item->order_id !== $order->id) {
            return response()->json(['error' => 'Este item não pertence a este pedido.'], 403);
        }

        $item->delete();
        return response()->json(['message' => 'Item removido com sucesso.']);
    }
}