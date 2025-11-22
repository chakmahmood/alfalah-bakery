<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'code' => 'BR001',
            'name' => 'Kantor Utama',
            'address' => 'Jl. Utama No. 1',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        Branch::create([
            'code' => 'BR002',
            'name' => 'Cabang 1',
            'address' => 'Jl. Cabang No. 1',
            'phone' => '081298765432',
            'is_active' => true,
        ]);
    }
}
