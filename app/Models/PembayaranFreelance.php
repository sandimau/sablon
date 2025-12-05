<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranFreelance extends Model
{
    use HasFactory;

    protected $fillable = [
        'freelance_id',
        'akun_detail_id',
        'tanggal',
        'tahun',
        'bulan',
        'total_upah',
        'total_lembur',
        'total_pembayaran',
        'potongan_kasbon',
        'total_keluar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_upah' => 'decimal:2',
        'total_lembur' => 'decimal:2',
        'total_pembayaran' => 'decimal:2',
        'potongan_kasbon' => 'decimal:2',
        'total_keluar' => 'decimal:2',
    ];

    public function freelance()
    {
        return $this->belongsTo(Freelance::class);
    }

    public function akunDetail()
    {
        return $this->belongsTo(AkunDetail::class);
    }
}
