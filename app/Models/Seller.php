<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'store_description',
        'logo'
    ];

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
