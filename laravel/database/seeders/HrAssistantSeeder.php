<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HrAssistantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::where('email', 'assistant@hrdwms.com')->exists()) {
            User::create([
                'name' => 'assistant',
                'email' => 'assistant@hrdwms.com',
                'password' => Hash::make('password'),
                'role' => 'hr_assistant',
                'is_active' => 1,
            ]);
        }
    }
}
