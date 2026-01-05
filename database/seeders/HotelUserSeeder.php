<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HotelUserSeeder extends Seeder
{
     public function run()
    {
        User::create([
            'name' => 'Shree Samarth',
            'email' => 'samarthhotel@gmail.com',
            'password' => Hash::make('samarthhotel123'),
            'email_verified_at' => now()
        ]);

        echo "samarth hotel user created successfully!\n";
        echo "Email: samarthhotel@gmail.com\n";
        echo "Password: samarthhotel123\n";
    }
}

