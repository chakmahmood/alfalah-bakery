<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Pcs', 'symbol' => 'pcs', 'conversion_factor' => 1],
            ['name' => 'Box', 'symbol' => 'box', 'conversion_factor' => 1],
            ['name' => 'Pack', 'symbol' => 'pack', 'conversion_factor' => 1],
            ['name' => 'Gram', 'symbol' => 'g', 'conversion_factor' => 1],
            ['name' => 'Kilogram', 'symbol' => 'kg', 'conversion_factor' => 1000],
            ['name' => 'Milligram', 'symbol' => 'mg', 'conversion_factor' => 0.001],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
