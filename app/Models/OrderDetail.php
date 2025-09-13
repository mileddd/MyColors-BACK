<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    public $timestamps = false;

    public $primaryKey = 'orderdetail_id';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_qty'
    ];
}
