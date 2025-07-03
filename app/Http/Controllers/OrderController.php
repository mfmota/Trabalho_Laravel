<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request) 
    {
        $orders = Order::where('draft', false)
                       ->where('status', false)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request) 
    {
        $order = Order::create($request->validated());
        return response()->json($order, 201);
    }

    public function show(Order $order) 
    {
        $order->load('items.product');
        return response()->json($order);
    }

    public function destroy(Order $order) //
    {
        $order->delete();
        return response()->json(['message' => 'Pedido deletado com sucesso.']);
    }

    public function send(Order $order) //
    {
        $order->update(['draft' => false]);
        return response()->json($order);
    }

    public function finish(Order $order) //
    {
        $order->update(['status' => true]);
        return response()->json($order);
    }
}