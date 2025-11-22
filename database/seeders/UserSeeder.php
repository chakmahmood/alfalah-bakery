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
        // Rootmaster
        $root = User::updateOrCreate(
            ['email' => 'rootmaster@pdk.com'],
            [
                'name' => 'Root Master',
                'password' => Hash::make('Overlord99'),
                'email_verified_at' => now(),
            ]
        );
        $root->assignRole('super_admin');

        // Superadmin
        $superadmin = User::updateOrCreate(
            ['email' => 'superadmin@roti.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('super12345'),
                'email_verified_at' => now(),
            ]
        );
        $superadmin->assignRole('superadmin');

        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@roti.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin12345'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Kasir
        $kasir = User::updateOrCreate(
            ['email' => 'kasir@roti.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('kasir12345'),
                'email_verified_at' => now(),
            ]
        );
        $kasir->assignRole('kasir');

    }
}
