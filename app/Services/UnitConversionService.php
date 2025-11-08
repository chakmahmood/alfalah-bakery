<?php

namespace App\Services;

use App\Models\Unit;

class UnitConversionService
{
    /**
     * Konversi jumlah dari satu unit ke unit lain
     *
     * @param float $quantity
     * @param string|int $fromUnitId
     * @param string|int $toUnitId
     * @return float
     * @throws \Exception
     */
    public static function convert(float $quantity, $fromUnitId, $toUnitId): float
    {
        if ($fromUnitId == $toUnitId) {
            return $quantity;
        }

        // Ambil rasio unit
        $fromUnit = Unit::findOrFail($fromUnitId);
        $toUnit = Unit::findOrFail($toUnitId);

        if (!$fromUnit->conversion_factor || !$toUnit->conversion_factor) {
            throw new \Exception("Unit conversion factor belum di-set untuk '{$fromUnit->name}' atau '{$toUnit->name}'");
        }

        // Konversi: ke base unit dulu (misal gram)
        $quantityInBase = $quantity * $fromUnit->conversion_factor;
        $converted = $quantityInBase / $toUnit->conversion_factor;

        return $converted;
    }
}
