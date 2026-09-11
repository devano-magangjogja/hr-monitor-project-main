<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (!User::where('email', 'dep@hrdwms.com')->exists()) {
            User::create([
                'name' => 'dep',
                'email' => 'dep@hrdwms.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => 1,
            ]);
        }
    }
}
