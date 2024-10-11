<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'slug',
        'code',
        'image',
        'description',
        'price',
        'stock',
    ];    

    public function seller(){
        return $this->belongsTo(Seller::class);
    }

    public function orders(){
        return $this->BelongsToMany(Order::class)->withPivot('quantity', 'price');
    }

    public function review(){
        return $this->hasMany(Review::class);
    }

    public function offer(){
        return $this->belongsTo(Offer::class);
    }
    public function orderProducts(){
        return $this->hasMany(OrderProduct::class, 'product_id');
    }
}
