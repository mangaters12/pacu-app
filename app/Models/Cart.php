<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// app/Models/Cart.php
class Cart extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

