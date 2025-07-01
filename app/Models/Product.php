<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['toko_id', 'nama', 'deskripsi', 'harga'];

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Simpan gambar (file upload) ke product_images dalam bentuk base64 langsung ke DB.
     * @param array $files Array file upload dari request->file('images')
     * @return void
     */
    public function saveImagesBase64(array $files)
    {
        foreach ($files as $file) {
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $mime = $file->getMimeType();
            $base64Image = "data:$mime;base64,$imageData";

            $this->images()->create([
                'image_path' => $base64Image,
            ]);
        }
    }
}
