<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel users.
     */
    public function run(): void
    {
        // Admin default
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'headcliff',
                'password' => Hash::make('abc321'),
                'email_verified_at' => now(),
            ]
        );

        // Contoh tambahan 10 user acak
        // User::factory(10)->create();
    }
}
