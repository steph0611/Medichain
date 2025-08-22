<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'pharmacy_id',
        'items',
        'status',
    ];

    protected $casts = [
        'items' => 'array', // Laravel auto-converts JSON
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'pharmacy_id', 'shop_id');
    }
}
