<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Okta',
            'email' => 'oktaharis@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
            'phone' => '085889473650',
            'points' => 10,
        ]);

        User::create([
            'name' => 'Employee',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'points' => 10,
        ]);
    }
}
