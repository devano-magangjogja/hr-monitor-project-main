<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HrStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::where('email', 'staff@hrdwms.com')->exists()) {
            User::create([
                'name' => 'staff',
                'email' => 'staff@hrdwms.com',
                'password' => Hash::make('password'),
                'role' => 'hr_staff',
                'is_active' => 1,
            ]);
        }
    }
}
