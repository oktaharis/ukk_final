<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock', 'image'];
    public function sales()
    {
        return $this->belongsToMany(Sale::class)->withPivot('quantity');
    }
}
