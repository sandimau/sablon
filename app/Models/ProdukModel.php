<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukModel extends Model
{
    public $table = 'produk_models';
    protected $guarded = [];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function produk()
    {
        return $this->hasMany(Produk::class, 'produk_model_id');
    }
}
