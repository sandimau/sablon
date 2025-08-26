<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Models\KategoriUtama;
use App\Http\Controllers\Controller;

class KategoriUtamaController extends Controller
{
    public function index()
    {
        $kategoriUtamas = KategoriUtama::all();

        return view('admin.kategoriUtama.index', compact('kategoriUtamas'));
    }

    public function create()
    {
        return view('admin.kategoriUtama.create');
    }

    public function store(Request $request)
    {
        KategoriUtama::create($request->all());

        return redirect()->route('kategoriUtama.index')->with('success', 'Kategori Utama berhasil ditambahkan');
    }

    public function edit(KategoriUtama $kategoriUtama)
    {
        return view('admin.kategoriUtama.edit', compact('kategoriUtama'));
    }

    public function update(Request $request, KategoriUtama $kategoriUtama)
    {
        $kategoriUtama->update($request->all());

        return redirect()->route('kategoriUtama.index')->with('success', 'Kategori Utama berhasil diupdate');
    }

    public function indexByKategoriUtama(KategoriUtama $kategoriUtama)
    {
        $kategoris = Kategori::where('kategori_utama_id', $kategoriUtama->id)->get();

        return view('admin.kategoris.indexKategoriUtama', compact('kategoris', 'kategoriUtama'));
    }

    public function createByKategoriUtama(KategoriUtama $kategoriUtama)
    {
        return view('admin.kategoris.createKategoris', compact('kategoriUtama'));
    }

    public function storeByKategoriUtama(Request $request, KategoriUtama $kategoriUtama)
    {
        Kategori::create($request->all());

        return redirect()->route('kategori.indexByKategoriUtama', $kategoriUtama)->with('success', 'Kategori berhasil ditambahkan');
    }

    public function editByKategoriUtama(KategoriUtama $kategoriUtama, Kategori $kategori)
    {
        return view('admin.kategoris.editKategoriUtama', compact('kategoriUtama', 'kategori'));
    }
}
