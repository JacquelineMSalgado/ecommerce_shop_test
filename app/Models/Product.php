<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = 
    ['name', 'slug', 'description', 'price', 'picture', 'status'];

    use HasFactory;

    public function orders() {
        return $this->belongsToMany(orders::class, 'products_orders', 'id' /* products */, 'id' /* orders */);
    }
}
