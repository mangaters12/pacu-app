<?php
// Di model Kurir.php, tambahkan accessor untuk URL foto
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurir extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'ktp_number',
        'plate_number',
        'address',
        'vehicle_type',
        'photo_path',
        'driver_license_number',
        'role'
    ];

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            // Menghasilkan URL publik dari path storage
            return asset('storage/' . substr($this->photo_path, 7)); // buang 'public/' dari path
        }
        return null; // atau URL placeholder gambar
    }
public function user()
{
    return $this->belongsTo(User::class);
}

}
