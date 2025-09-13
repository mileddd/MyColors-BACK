<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\ProductService;

class ProductController extends ApiController
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function fetchProducts(Request $request)
    {
        $products = $this->productService->fetchAll();

        return $this->successResponse($products, 200);
    }
}
