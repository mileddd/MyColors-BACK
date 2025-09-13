<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function fetchAll()
    {
        return Product::orderBy('product_name')->get();
    }
}