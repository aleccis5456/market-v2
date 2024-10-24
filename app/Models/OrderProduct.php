<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    public $table = 'order_products';
    protected $fillable = [
        'order_id', 	
        'product_id', 	
        'quantity', 	
        'price'
    ];

    // public function products(){
    //     return $this->hasMany(Product::class, 'product_id');
    // }
}
