<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function calculateTarif()
    {
        if ($this->status === 'keluar' && $this->waktu_keluar) {
            $duration = $this->waktu_masuk->diffInHours($this->waktu_keluar, true);
            $rate = $this->kendaraan->jenis_kendaraan === 'motor' ? 2000 : 5000;
            return ceil($duration) * $rate;
        }
        return 0;
    }
}
