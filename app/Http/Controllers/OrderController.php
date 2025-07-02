<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Lista os pedidos (substitui ListOrderController)
    public function index(Request $request) //
    {
        // Filtra por pedidos que não são rascunho e ordena pelos mais recentes
        $orders = Order::where('draft', false)
                       ->where('status', false)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json($orders);
    }

    // Cria um novo pedido (substitui CreateOrderController)
    public function store(StoreOrderRequest $request) //
    {
        $order = Order::create($request->validated());
        return response()->json($order, 201);
    }

    // Mostra os detalhes de um pedido (substitui DetailOrderController)
    public function show(Order $order) //
    {
        // Carrega os relacionamentos de itens e os produtos dentro dos itens
        $order->load('items.product');
        return response()->json($order);
    }

    // Deleta um pedido (substitui DeleteOrderController)
    public function destroy(Order $order) //
    {
        // Adicionar lógica aqui para garantir que só se pode deletar rascunhos, por exemplo
        if (!$order->draft) {
            return response()->json(['error' => 'Não é possível deletar um pedido já enviado.'], 403);
        }
        $order->delete();
        return response()->json(['message' => 'Pedido deletado com sucesso.']);
    }

    // Avança o status do pedido (substitui SendOrderController)
    public function send(Order $order) //
    {
        $order->update(['draft' => false]);
        return response()->json($order);
    }

    // Finaliza o pedido (substitui FinishOrderController)
    public function finish(Order $order) //
    {
        $order->update(['status' => true]);
        return response()->json($order);
    }
}