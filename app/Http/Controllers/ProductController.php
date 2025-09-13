<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Product;

class ProductController extends ApiController
{
    public function fetchProducts(Request $request)
    {
        $products = Product::orderBy('product_name')->get();

        return ApiController::successResponse($products,200);
    }
}
