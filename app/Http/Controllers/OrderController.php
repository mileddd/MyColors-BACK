<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\CancelOrderRequest;

class OrderController extends ApiController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createOrder(Request $request)
    {
        $result = $this->orderService->createOrder($request->user()->id);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], $result['code']);
        }

        return $this->successResponse("OK", 200);
    }

    public function fetchOrders(Request $request)
    {
        $orders = $this->orderService->fetchOrders($request->user()->id);
        return $this->successResponse($orders, 200);
    }

    public function cancelOrder(CancelOrderRequest $request)
    {
        $result = $this->orderService->cancelOrder($request->orderId);

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], $result['code']);
        }

        return $this->successResponse("OK", 200);
    }
}
