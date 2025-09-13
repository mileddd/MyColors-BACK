<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
    use HasFactory;

    protected $table = 'user_cart';

    public $timestamps = false;

    public $primaryKey = 'usercart_id';

    protected $fillable = [
        'product_id',
        'user_id'
    ];
}
