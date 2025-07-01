<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\OrderDetail;

class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'total_price'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
public function payment()
    {
        return $this->hasOne(Payment::class);
    }
public function kurir()
{
    return $this->belongsTo(Kurir::class, 'kurir_id');
}


}
