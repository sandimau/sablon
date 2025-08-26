<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProdukModel;
use App\Models\KategoriUtama;
use App\Http\Controllers\Controller;

class ProdukModelController extends Controller
{
    public function index(KategoriUtama $kategoriUtama)
    {
        $produkModels = ProdukModel::where('kategori_id',$kategoriUtama->id)->get();

        return view('admin.produkModels.index', compact('produkModels','kategoriUtama'));
    }
}
