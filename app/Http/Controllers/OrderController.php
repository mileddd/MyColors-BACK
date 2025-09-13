<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;

class OrderController extends ApiController
{
    public function createOrder(Request $request)
    {
        $user = $request->user();
        $itemsOutOfStock = Array();
        $getUserCartProduct = UserCart::join('products','products.product_id','=','user_cart.product_id')
        ->where([
            ['user_id','=',$user->id]
        ])
        ->get();

        if( !count( $getUserCartProduct ) )
        {
            return ApiController::errorResponse("Your cart is empty. ", 422);
        }

        foreach ($getUserCartProduct as $cartItem) {
            $product = Product::find($cartItem->product_id);
        
            if (!$product) {
                // Product does not exist
                $itemsOutOfStock[] = "Product ID {$cartItem->product_id} not found";
            }
        
            if ($product->product_stock < $cartItem->usercart_qty) {
                if( $product->product_stock > 0 )
                {
                    $itemsOutOfStock[] = "Insufficient stock for {$product->product_name}, only {$product->product_stock} left";
                }
                else
                {
                    $itemsOutOfStock[] = "Insufficient stock for {$product->product_name}";
                }
            }
        }

        if( count( $itemsOutOfStock ) )
        {
            return ApiController::errorResponse($itemsOutOfStock, 422);
        }

        DB::beginTransaction();
        // Calculate total amount
        try {
            $totalAmount = $getUserCartProduct->sum(function($cartItem) {
                return $cartItem->product_price * $cartItem->usercart_qty;
            });

            $order = Array(
                'order_date' => date('Y-m-d H:i:s'),
                'user_id' => $user->id,
                'order_amount' => $totalAmount
            );
            $order = Order::create($order);

            if ($order) {
                // Prepare all order details
                $orderDetails = $getUserCartProduct->map(function ($cart) use ($order) {
                    return [
                        'order_id'   => $order->order_id,
                        'product_id' => $cart->product_id,
                        'product_qty'=> $cart->usercart_qty
                    ];
                })->toArray();
            
                // Bulk insert all details
                OrderDetail::insert($orderDetails);

                foreach ($getUserCartProduct as $cartItem) {
                    Product::where('product_id', $cartItem->product_id)
                        ->decrement('product_stock', $cartItem->usercart_qty);
                }
            }

            UserCart::where([
                ['user_id','=',$user->id]
            ])
            ->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiController::errorResponse("Failed to create order: {$e->getMessage()}", 500);
        }

        return ApiController::successResponse("OK",200);
    }

    public function fetchOrders(Request $request)
    {
        $user = $request->user();
        
        $orders = Order::where([
            ['user_id','=',$user->id]
        ])
        ->orderBy('order_date','DESC')
        ->get();

            // Fetch order details for all orders
        $orderIds = $orders->pluck('order_id')->toArray();

        $orderDetails = OrderDetail::join('products', 'order_details.product_id', '=', 'products.product_id')
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

        // Attach order details to each order
        $orders = $orders->map(function ($order) use ($orderDetails) {
            $details = $orderDetails->where('order_id', $order->order_id)->values();
            $order->order_details = $details;
            return $order;
        });

        return ApiController::successResponse($orders, 200);
    }

    public function cancelOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderId' => 'required'
        ]);

        if ($validator->fails()) {
            return ApiController::errorResponse($validator->errors(), 401);
        }

        $checkOrderStatus = Order::where([
            ['order_id','=',$request->orderId]
        ])
        ->first();

        if( $checkOrderStatus && $checkOrderStatus->order_status == 's' )
        {
            return ApiController::errorResponse("Order is already shipped !", 422);
        }

        if ($checkOrderStatus && $checkOrderStatus->order_canceled) {
            return $this->errorResponse("Order has already been canceled!", 422);
        }

        $updateOrderCancelation = Order::where([
            ['order_id','=',$request->orderId]
        ])
        ->update([
            "order_canceled" => true
        ]);

        return ApiController::successResponse($updateOrderCancelation, 200);
    }
}
