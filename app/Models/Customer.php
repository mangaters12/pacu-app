<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['toko_id', 'nama', 'email'];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }
}
