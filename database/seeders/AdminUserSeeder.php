<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'dcadmin@gmail.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
        ]);
        echo "Admin user created successfully!\n";
    }
}


