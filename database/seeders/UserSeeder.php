<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * ユーザーのダミーデータを作成する
     */
    public function run(): void
    {
        User::forceCreate([
            'name' => '山田 太郎',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::forceCreate([
            'name' => '鈴木 花子',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::forceCreate([
            'name' => '田中 一郎',
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'admin_status' => true, 
        ]);
    }
}