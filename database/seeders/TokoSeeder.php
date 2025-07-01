<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Toko;

class TokoSeeder extends Seeder
{
    public function run()
    {
        Toko::create(['nama' => 'Toko A', 'alamat' => 'Jl. Toko A']);
        Toko::create(['nama' => 'Toko B', 'alamat' => 'Jl. Toko B']);
    }
}
