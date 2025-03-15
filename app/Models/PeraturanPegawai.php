<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeraturanPegawai extends Model
{
    protected $fillable = [
        'judul',
        'isi',
        'tanggal_berlaku',
        'status'
    ];

    protected $casts = [
        'tanggal_berlaku' => 'date'
    ];
}