<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    public $table = 'offers';
    protected $fillable = [
        'product_id', 	
        'discount_type', 	
        'discount_value', 	
        'start_date', 	
        'end_date',
        'active'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
