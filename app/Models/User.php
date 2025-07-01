<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

  protected $fillable = [
      'name', 'email', 'password', 'role', 'toko_id', 'alamat', 'lat', 'long'
  ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Relasi ke model Toko
    public function toko()
    {
        return $this->hasOne(Toko::class, 'user_id');
    }

    // Relasi ke model Kurir
    public function kurir()
    {
        return $this->hasOne(Kurir::class);
    }

    // Cek role (utama diambil dari kurir->role, fallback user->role)
    public function hasRole($role)
    {
        if ($this->kurir && $this->kurir->role === $role) {
            return true;
        }

        return $this->role === $role;
    }
}
