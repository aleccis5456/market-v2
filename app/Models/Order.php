<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'code',
        'shipping_address_id',
        'total',
        'status',
    ];    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price');
    }

    public function shippingAdress(){
        return $this->belongsTo(ShippingAdress::class);
    }
}
