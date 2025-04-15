<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'TV butut',
            'price' => 10000,
            'stock' => 50,
            
        ]);
        Product::create([
            'name' => 'TV asdj',
            'price' => 10000,
            'stock' => 50,
        ]);
        Product::create([
            'name' => 'TV askdj',
            'price' => 10000,
            'stock' => 50,
        ]);
        Product::create([
            'name' => 'TV bagus',
            'price' => 10000,
            'stock' => 50,
        ]);
    }
}
