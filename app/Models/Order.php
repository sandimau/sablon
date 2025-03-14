<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'orders';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        Order::saving(function ($model) {

            $total = 0;

            $batal = Produksi::where('nama', 'batal')->first()->id;

            foreach ($model->orderDetail as $detail) {

                if ($detail->produksi_id != $batal) {
                    $total += $detail->jumlah * $detail->harga;
                }
            }
            $model->total = $total - $model->diskon + $model->ongkir;
        });
    }

    public function getKekuranganAttribute()
    {
        return $this->total - $this->bayar;
    }

    public function kontak()
    {
        return $this->belongsTo(Kontak::class, 'kontak_id');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function scopeOffline($query)
    {
        return $query->whereHas('kontak', function($q) {
            $q->where('marketplace', 0)
              ->orWhereNull('marketplace');
        })
        ->orderBy('id', 'desc');
    }

    public function scopeOnline($query)
    {
        return $query->whereHas('kontak', function($q) {
            $q->where('marketplace', '!=', 0);
        })
        ->orderBy('id', 'desc');
    }

    public function scopeBelumLunas($query)
    {
        return $query->whereRaw('total > bayar')
            ->whereHas('kontak', function($q) {
                $q->where('marketplace', 0)
                  ->orWhereNull('marketplace');
            })
            ->orderBy('id', 'desc');
    }

    public function getListprodukAttribute()
    {
        $yy = array();
        foreach ($this->orderDetail as $item) {
            $yy[] = $item->produk ? $item->produk->nama : '';
        }
        return implode(', ', $yy);
    }

    public function scopeOmzetTahun($query)
    {
        $query->select(DB::raw('YEAR(created_at) as year'), DB::raw('SUM(total) as sum'));
        $query->whereRaw('total');
        $query->groupBy('year');
        return $query;
    }

    public function scopeOmzetBulan($query, $var)
    {
        $finish = Produksi::ambilFlow('batal');
        $query->select(
            DB::raw('YEAR(orders.created_at) as year'),
            DB::raw('EXTRACT(YEAR_MONTH FROM orders.created_at) as month'),
            DB::raw('MONTHNAME(orders.created_at) as monthname'),
            DB::raw('SUM(order_details.jumlah * order_details.harga) as omzet')
        )
        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
        ->join('produksis', 'order_details.produksi_id', '=', 'produksis.id')
        ->where(DB::raw('YEAR(orders.created_at)'), '=', $var)
        ->where('produksis.id', '<>', $finish)
        ->groupBy('month')
        ->orderBy('orders.created_at');
        return $query;
    }
}
