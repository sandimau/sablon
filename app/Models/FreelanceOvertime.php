<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelanceOvertime extends Model
{
    use HasFactory;

    protected $fillable = [
        'freelance_id',
        'akun_detail_id',
        'jam_lembur',
        'jumlah_upah',
        'keterangan',
        'kategori',
        'status',
        'catatan_akunting',
    ];

    public function freelance(){
        return $this->belongsTo(Freelance::class);
    }

    public function akunDetail(){
        return $this->belongsTo(AkunDetail::class);
    }
}
