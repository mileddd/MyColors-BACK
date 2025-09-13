<?php

namespace App\Http\Controllers;

use App\Services\UserCartService;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\ChangeCartQtyRequest;
use Illuminate\Http\Request;

class UserCartController extends ApiController
{
    protected $cartService;

    public function __construct(UserCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function fetchUserCart(Request $request)
    {
        $cart = $this->cartService->fetchUserCart($request->user()->id);
        return $this->successResponse($cart, 200);
    }

    public function addToCart(AddToCartRequest $request)
    {
        $result = $this->cartService->addToCart($request->user()->id, $request->productId);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], $result['code']);
        }

        return $this->successResponse("OK", 200);
    }

    public function changeUserProductQty(ChangeCartQtyRequest $request)
    {
        $result = $this->cartService->changeUserProductQty(
            $request->user()->id,
            $request->productId,
            $request->type
        );

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], $result['code']);
        }

        return $this->successResponse("OK", 200);
    }
}
