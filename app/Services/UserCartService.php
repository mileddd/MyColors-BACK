<?php

namespace App\Services;

use App\Models\Product;
use App\Models\UserCart;

class UserCartService
{
    public function fetchUserCart($userId)
    {
        return UserCart::join('products','products.product_id','=','user_cart.product_id')
            ->where('user_id','=',$userId)
            ->orderBy('product_name')
            ->get();
    }

    public function addToCart($userId, $productId)
    {
        $product = Product::where('product_id', $productId)
            ->where('product_stock', '>', 0)
            ->first();

        if (!$product) {
            return ['error' => "Out of stock !", 'code' => 422];
        }

        $cartItem = UserCart::where([
            ['user_id', $userId],
            ['product_id', $productId]
        ])->first();

        if ($cartItem) {
            if ($cartItem->usercart_qty >= $product->product_stock) {
                return ['error' => "Out of stock !", 'code' => 422];
            }
            $cartItem->increment('usercart_qty');
        } else {
            UserCart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'usercart_qty' => 1
            ]);
        }

        return ['success' => true];
    }

    public function changeUserProductQty($userId, $productId, $type)
    {
        $product = Product::where('product_id', $productId)
            ->where('product_stock', '>', 0)
            ->first();

        $cartItem = UserCart::where([
            ['user_id', $userId],
            ['product_id', $productId]
        ])->first();

        if (!$cartItem) {
            return ['error' => "Item not in cart", 'code' => 422];
        }

        if ($type === 'plus') {
            if (!$product || $cartItem->usercart_qty >= $product->product_stock) {
                return ['error' => "Out of stock !", 'code' => 422];
            }
            $cartItem->increment('usercart_qty');
        } else {
            $cartItem->decrement('usercart_qty');
        }

        if ($cartItem->usercart_qty <= 0) {
            $cartItem->delete();
        }

        return ['success' => true];
    }
}
