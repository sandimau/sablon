<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelance extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'tanggal_masuk',
        'upah',
        'handphone',
        'nomor_rekening',
        'bank',
        'is_active',
        'rate_lembur_per_jam',
        'user_id',
    ];

    protected $casts = [
        'nama'           => 'string',
        'alamat'         => 'string',
        'tanggal_masuk'  => 'date',
        'upah'           => 'decimal:2',
        'handphone'      => 'string',
        'nomor_rekening' => 'string',
        'bank'           => 'string',
        'is_active'      => 'boolean',
        'rate_lembur_per_jam' => 'decimal:2',
    ];

    public function freelanceOvertimes(){
        return $this->hasMany(FrelanceOvertime::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
