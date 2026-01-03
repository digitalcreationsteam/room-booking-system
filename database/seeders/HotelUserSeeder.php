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
            'email' => 'samarthhotel@com.com',
            'password' => Hash::make('samarthhotel123'),
            'email_verified_at' => now()
        ]);

        echo "samarth hotel user created successfully!\n";
        echo "Email: samarthhotel@com.com\n";
        echo "Password: samarthhotel123\n";
    }
}

