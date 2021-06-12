<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsOrders extends Model
{
    protected $fillable = 
    ['actualPrice', 'product_id', 'order_id', 'status'];

    use HasFactory;
}
