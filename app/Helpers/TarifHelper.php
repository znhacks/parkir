<?php

namespace App\Helpers;

class TarifHelper
{
    /**
     * Get tariff based on vehicle type
     * 
     * @param string $jenisKendaraan
     * @return int
     */
    public static function getTarifByJenis($jenisKendaraan)
    {
        $tarif = [
            'motor' => 2000,
            'mobil' => 4000,
        ];

        return $tarif[$jenisKendaraan] ?? 0;
    }

    /**
     * Calculate tariff for parking exit
     * 
     * @param \App\Models\Parkir $parkir
     * @return int
     */
    public static function calculateExitTarif($parkir)
    {
        if (!$parkir->kendaraan) {
            return 0;
        }

        return self::getTarifByJenis($parkir->kendaraan->jenis_kendaraan);
    }
}
