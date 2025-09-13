<?php

namespace App\Services;

use App\Models\Product;
use App\Models\UserCart;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder($userId)
    {
        $itemsOutOfStock = [];
        $cartItems = UserCart::join('products','products.product_id','=','user_cart.product_id')
            ->where('user_id', $userId)
            ->get();

        if ($cartItems->isEmpty()) {
            return ['error' => "Your cart is empty.", 'code' => 422];
        }

        // Stock check
        foreach ($cartItems as $cartItem) {
            $product = Product::find($cartItem->product_id);

            if (!$product) {
                $itemsOutOfStock[] = "Product ID {$cartItem->product_id} not found";
            } elseif ($product->product_stock < $cartItem->usercart_qty) {
                if ($product->product_stock > 0) {
                    $itemsOutOfStock[] = "Insufficient stock for {$product->product_name}, only {$product->product_stock} left";
                } else {
                    $itemsOutOfStock[] = "Insufficient stock for {$product->product_name}";
                }
            }
        }

        if (!empty($itemsOutOfStock)) {
            return ['error' => $itemsOutOfStock, 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $totalAmount = $cartItems->sum(fn($item) => $item->product_price * $item->usercart_qty);

            $order = Order::create([
                'order_date' => now(),
                'user_id' => $userId,
                'order_amount' => $totalAmount,
            ]);

            if ($order) {
                $orderDetails = $cartItems->map(fn($cart) => [
                    'order_id'   => $order->order_id,
                    'product_id' => $cart->product_id,
                    'product_qty'=> $cart->usercart_qty
                ])->toArray();

                OrderDetail::insert($orderDetails);

                foreach ($cartItems as $cartItem) {
                    Product::where('product_id', $cartItem->product_id)
                        ->decrement('product_stock', $cartItem->usercart_qty);
                }
            }

            UserCart::where('user_id', $userId)->delete();

            DB::commit();
            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => "Failed to create order: {$e->getMessage()}", 'code' => 500];
        }
    }

    public function fetchOrders($userId)
    {
        $orders = Order::where('user_id', $userId)
            ->orderBy('order_date', 'DESC')
            ->get();

        $orderIds = $orders->pluck('order_id')->toArray();

        $details = OrderDetail::join('products', 'order_details.product_id', '=', 'products.product_id')
            ->whereIn('order_id', $orderIds)
            ->select(
                'order_details.order_id',
                'order_details.product_id',
                'order_details.product_qty',
                'products.product_name',
                'products.product_price',
                'products.product_stock'
            )
            ->get();

        return $orders->map(function ($order) use ($details) {
            $order->order_details = $details->where('order_id', $order->order_id)->values();
            return $order;
        });
    }

    public function cancelOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return ['error' => "Order not found", 'code' => 404];
        }

        if ($order->order_status === 's') {
            return ['error' => "Order is already shipped!", 'code' => 422];
        }

        if ($order->order_canceled) {
            return ['error' => "Order has already been canceled!", 'code' => 422];
        }

        $order->update(['order_canceled' => true]);

        return ['success' => true];
    }
}
