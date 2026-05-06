<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $fillable = ['nomor_plat', 'jenis_kendaraan'];

    public function parkirs()
    {
        return $this->hasMany(Parkir::class);
    }
}
