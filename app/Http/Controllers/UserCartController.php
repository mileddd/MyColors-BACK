<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserCartController extends ApiController
{
    public function fetchUserCart(Request $request)
    {
        $cart = UserCart::join('products','products.product_id','=','user_cart.product_id')
        ->orderBy('product_name')
        ->get();

        return ApiController::successResponse($cart,200);
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required'
        ]);

        if ($validator->fails()) {
            return ApiController::errorResponse($validator->errors(), 401);
        }

        $user = $request->user();

        $checkIfProductIsAvailable = Product::where([
            ['product_id','=',$request->productId],
            ['product_stock','>',0]
        ])
        ->first();

        if( !$checkIfProductIsAvailable )
        {
            return ApiController::errorResponse("Out of stock !",422);
        }

        $checkIfProductAlreadyAdded = UserCart::where([
            ['user_id','=',$user->id],
            ['product_id',$request->productId]
        ])
        ->first();

        if( $checkIfProductAlreadyAdded )
        {
            if( $checkIfProductAlreadyAdded->usercart_qty >= $checkIfProductIsAvailable->product_stock )
            {
                return ApiController::errorResponse("Out of stock !",422);
            }
            UserCart::where([
                ['user_id','=',$user->id],
                ['product_id',$request->productId]
            ])
            ->increment('usercart_qty', 1);
        }
        else
        {
            $userCart = Array(
                'product_id' => $request->productId,
                'user_id'    => $user->id
            );
            $createCart = UserCart::create($userCart);
        }

        return ApiController::successResponse("OK",200);
    }

    public function changeUserProductQty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'productId' => 'required'
        ]);

        if ($validator->fails()) {
            return ApiController::errorResponse($validator->errors(), 401);
        }

        $user = $request->user();

        $checkIfProductIsAvailable = Product::where([
            ['product_id','=',$request->productId],
            ['product_stock','>',0]
        ])
        ->first();

        $checkIfProductAlreadyAdded = UserCart::where([
            ['user_id','=',$user->id],
            ['product_id',$request->productId]
        ])
        ->first();

        if( $request->type == 'plus' )
        {
            if( ! $checkIfProductIsAvailable || ($checkIfProductAlreadyAdded->usercart_qty >= $checkIfProductIsAvailable->product_stock) )
            {
                return ApiController::errorResponse("Out of stock !",422);
            }

            UserCart::where([
                ['user_id','=',$user->id],
                ['product_id',$request->productId]
            ])
            ->increment('usercart_qty', 1);

        }
        else
        {
            UserCart::where([
                ['user_id','=',$user->id],
                ['product_id',$request->productId]
            ])
            ->decrement('usercart_qty', 1);
        }

        UserCart::where([
            ['user_id','=',$user->id],
            ['product_id',$request->productId],
            ['usercart_qty','=',0]
        ])
        ->delete();

        return ApiController::successResponse("OK",200);

    }
}
