<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = [
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ];
        
        $createdAdmin = User::firstOrCreate(['email' => 'admin@example.com'],$admin);

        $createdAdmin->assignRole('admin');

        $user = [
            'name' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ];

        $createdUser = User::firstOrCreate(['email' => 'user@example.com'],$user);

        $createdUser->assignRole('user');
    }
}
