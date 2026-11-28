<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorprizeWinner extends Model
{
    use HasFactory;

    protected $table = 'doorprize_winners';

    protected $fillable = [
        'attendance_id',
        'nik',
        'nama_pegawai',
        'departemen',
        'hadiah',
        'waktu_menang',
    ];

    protected $casts = [
        'waktu_menang' => 'datetime',
    ];
}
