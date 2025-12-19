<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@izmirtimemachine.local'],
            [
                'name' => 'Admin',
                'password' => '123456', // hashed via cast in User model
            ]
        );
    }
}


