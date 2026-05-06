<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\TarifHelper;

class Parkir extends Model
{
    use HasFactory;

    protected $fillable = [
        'kendaraan_id',
        'waktu_masuk',
        'waktu_keluar',
        'status',
        'tarif',
        'user_id'
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function histories()
    {
        return $this->hasMany(ParkirHistory::class);
    }

    /**
     * Calculate tarif based on vehicle type
     * 
     * @return int
     */
    public function calculateTarif()
    {
        return TarifHelper::calculateExitTarif($this);
    }

    /**
     * Get tarif by vehicle type
     * 
     * @return int
     */
    public function getTarifByVehicleType()
    {
        if (!$this->kendaraan) {
            return 0;
        }

        return TarifHelper::getTarifByJenis($this->kendaraan->jenis_kendaraan);
    }
}
