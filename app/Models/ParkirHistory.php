<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkirHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parkir_id',
        'aksi',
        'data_lama',
        'data_baru',
        'user_id'
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    public function parkir()
    {
        return $this->belongsTo(Parkir::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
