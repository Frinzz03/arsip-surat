<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@dispendik.com',
            'password' => Hash::make('@AdminDispendik2026'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // User::create([
        //     'name' => 'Staf Arsip',
        //     'email' => 'staf@dispendik.com',
        //     'password' => Hash::make('@StafArsip2026'),
        //     'role' => 'staf',
        //     'email_verified_at' => now(),
        // ]);
    }
}
