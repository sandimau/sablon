<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuBesar extends Model
{
    use HasFactory;

    public $table = 'buku_besars';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        BukuBesar::saving(function ($model) {

            $terakhir = $model->where('akun_detail_id', $model->akun_detail_id)->latest('id')->first()->saldo ?? 0;
            $model->saldo = $terakhir + $model->debet - $model->kredit;

            $akunDetail = AkunDetail::find($model->akun_detail_id);
            $akunDetail->update(['saldo' => $model->saldo]);
        });
    }

    public function getKetAttribute()
    {
        if ($this->detail_id) {
            if ($this->kode == 'byr') {
                if ($this->detail_id != 123) {
                    return "<a href='" . url('admin/order/' . $this->detail_id) . "/detail'>" .
                        $this->attributes['ket'];
                } else {
                    return $this->attributes['ket'];
                }
            } else if ($this->kode == 'blj') {
                return "<a href='" . url('admin/belanja/' . $this->detail_id) . "'>" .
                    $this->attributes['ket'];
            } else if ($this->kode == 'freelance') {
                return "<a href='" . url('admin/freelances/' . $this->detail_id) . "?tab=riwayat_pembayaran'>" .
                    $this->attributes['ket'];
            } else {
                return $this->attributes['ket'];
            }
        } else {
            if ($this->kode == 'byr') {
                if ($this->detail_id != 123) {
                    $pembayaran = Pembayaran::where('jumlah',$this->debet)->latest('id')->first();
                    return "<a href='" . url('admin/order/' . $pembayaran->order_id) . "/detail'>" .
                        $this->attributes['ket'];
                } else {
                    return $this->attributes['ket'];
                }
            } else if ($this->kode == 'blj') {
                $belanja = Belanja::where('total',$this->kredit)->latest('id')->first();
                return "<a href='" . url('admin/belanja/' . $belanja->id) . "'>" .
                    $this->attributes['ket'];
            } else if ($this->kode == 'freelance') {
                $freelance = PembayaranFreelance::where('total_keluar',$this->kredit)->latest('id')->first();
                return "<a href='" . url('admin/freelances/' . $freelance->freelance_id) . "?tab=riwayat_pembayaran'>" .
                    $this->attributes['ket'];
            } else {
                return $this->attributes['ket'];
            }
        }
    }
}
