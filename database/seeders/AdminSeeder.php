<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
     public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now()
        ]);

        echo "Admin user created successfully!\n";
        echo "Email: admin@hotel.com\n";
        echo "Password: password123\n";
    }
}

