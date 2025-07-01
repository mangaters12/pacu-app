<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Toko;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@shop.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        // Toko admin
        $tokoA = Toko::first();
        User::create([
            'name' => 'Toko Admin A',
            'email' => 'tokoA@shop.com',
            'password' => Hash::make('password'),
            'role' => 'toko',
            'toko_id' => $tokoA->id,
        ]);
        // User biasa
        User::create([
            'name' => 'User B',
            'email' => 'userb@shop.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
