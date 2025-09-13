<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    public $timestamps = false;

    public $primaryKey = 'order_id';

    protected $fillable = [
        'order_date',
        'user_id',
        'order_status',
        'order_amount'
    ];
}
