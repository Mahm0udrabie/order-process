<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrderPayment;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderProcessController extends Controller
{
    public function processOrder() {
        // create random order
        $order = Order::factory()->create();

        // process created random order
        ProcessOrderPayment::dispatch($order->id);

        return "Order Created Successfully.<hr> Order ID:{$order->id}<hr> User: {$order?->user?->name}";
    }
}
