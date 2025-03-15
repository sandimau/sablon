<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeraturanPegawai;
use Illuminate\Http\Request;

class PeraturanPegawaiController extends Controller
{
    public function index()
    {
        $peraturans = PeraturanPegawai::latest()->get();
        return view('admin.peraturan.index', compact('peraturans'));
    }

    public function create()
    {
        return view('admin.peraturan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'tanggal_berlaku' => 'required|date',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        PeraturanPegawai::create($request->all());

        return redirect()->route('peraturan.index')
            ->with('success', 'Peraturan pegawai berhasil ditambahkan');
    }

    public function edit(PeraturanPegawai $peraturan)
    {
        return view('admin.peraturan.edit', compact('peraturan'));
    }

    public function update(Request $request, PeraturanPegawai $peraturan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'tanggal_berlaku' => 'required|date',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $peraturan->update($request->all());

        return redirect()->route('peraturan.index')
            ->with('success', 'Peraturan pegawai berhasil diperbarui');
    }

    public function destroy(PeraturanPegawai $peraturan)
    {
        $peraturan->delete();

        return redirect()->route('peraturan.index')
            ->with('success', 'Peraturan pegawai berhasil dihapus');
    }
}