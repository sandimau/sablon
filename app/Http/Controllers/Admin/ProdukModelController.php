<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kontak;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProdukModelController extends Controller
{
    protected $satuan = [
        'Pcs',
        'Box',
        'Lusin',
        'Pack',
        'Kg',
        'Gram',
        'Liter',
        'Meter',
        'Roll',
        'Unit',
        'Set',
        'Karton'
    ];

    public function index(Kategori $kategori)
    {
        $produks = Produk::select(
            'produks.id as produk_id',
            'produks.nama as varian',
            'produks.hpp as hpp',
            'produk_models.nama as model',
            'produk_models.harga',
            'produk_models.satuan',
            'produk_models.deskripsi',
            'produk_models.jual',
            'produk_models.beli',
            'produk_models.stok',
            'produk_models.id as model_id',
            'produk_models.gambar',
            'produk_models.kategori_id',
            'produk_models.kontak_id',
            'produk_last_stoks.saldo as saldo',
            'belanja_details.harga as harga_beli'
        )
            ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
            ->leftJoin('produk_last_stoks', 'produks.id', '=', 'produk_last_stoks.produk_id')
            ->leftJoin('belanja_details', function ($join) {
                $join->on('produks.id', '=', 'belanja_details.produk_id')
                    ->whereRaw('belanja_details.id = (SELECT id FROM belanja_details WHERE produk_id = produks.id ORDER BY id DESC LIMIT 1)');
            })
            ->where('produk_models.kategori_id', $kategori->id)
            ->get();

        return view('admin.produkModels.index', compact('produks', 'kategori'));
    }

    public function create(Kategori $kategori)
    {
        $satuan = $this->satuan;
        return view('admin.produkModels.create', compact('kategori', 'satuan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
            'satuan' => 'required',
            'deskripsi' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $img = $request->file('gambar');
            $filename = time() . '.' . $request->gambar->extension();
            $img_resize = Image::make($img->getRealPath());
            $img_resize->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $save_path = 'uploads/produk/';
            if (!file_exists($save_path)) {
                mkdir($save_path, 666, true);
            }
            $img_resize->save(public_path($save_path . $filename));
            $gambar = $filename;
        }

        $kategori = Kategori::find($request->kategori_id);

        $validated['kategori_id'] = $request->kategori_id;
        $validated['jual'] = $kategori->kategoriUtama->jual;
        $validated['beli'] = $kategori->kategoriUtama->beli;
        $validated['stok'] = $kategori->kategoriUtama->stok;
        $validated['produksi'] = $kategori->kategoriUtama->produksi;
        $validated['gambar'] = $gambar;

        $produkModel = ProdukModel::create($validated);

        // Tambahkan data ke tabel produk
        Produk::create([
            'status' => 1,
            'produk_model_id' => $produkModel->id
        ]);
        return redirect()->route('produkModel.index', ['kategori' => $kategori->id])->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Kategori $kategori, $id)
    {
        $produkModel = ProdukModel::find($id);
        return view('admin.produkModels.show', compact('produkModel', 'kategori'));
    }

    public function edit(ProdukModel $produkModel)
    {
        $kategori = Kategori::find($produkModel->kategori_id);
        $kategoris = Kategori::where('kategori_utama_id',$kategori->kategori_utama_id)->get();
        $kontaks = Kontak::all();
        $satuan = $this->satuan;
        return view('admin.produkModels.edit', [
            'produkModel' => $produkModel,
            'kategori' => $kategori,
            'kategoris' => $kategoris,
            'kontaks' => $kontaks,
            'satuan' => $satuan
        ]);
    }

    public function update(Request $request, ProdukModel $produkModel)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
            'satuan' => 'required',
            'deskripsi' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $gambar = $produkModel->gambar;
        if ($request->hasFile('gambar')) {
            $img = $request->file('gambar');
            $filename = time() . '.' . $request->gambar->extension();
            $img_resize = Image::make($img->getRealPath());
            $img_resize->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $save_path = 'uploads/produk/';
            if (!file_exists($save_path)) {
                mkdir($save_path, 666, true);
            }
            $img_resize->save(public_path($save_path . $filename));
            $gambar = $filename;
        }

        $kategori = Kategori::find($request->kategori_id);

        $validated['kategori_id'] = $request->kategori_id;
        $validated['jual'] = $kategori->kategoriUtama ? $kategori->kategoriUtama->jual : 0;
        $validated['beli'] = $kategori->kategoriUtama ? $kategori->kategoriUtama->beli : 0;
        $validated['stok'] = $kategori->kategoriUtama ? $kategori->kategoriUtama->stok : 0;
        $validated['gambar'] = $gambar;
        $produkModel->update($validated);
        return redirect()->route('produkModel.index', ['kategori' => $kategori->id])->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(ProdukModel $produkModel)
    {
        if ($produkModel->gambar) {
            Storage::delete('public/' . $produkModel->gambar);
        }
        $produk = Produk::where('produk_model_id', $produkModel->id)->first();
        if ($produk) {
            $produk->delete();
        }
        $produkModel->delete();
        return redirect()->route('produkModel.index', ['kategori' => $produkModel->kategori->id])->with('success', 'Produk berhasil dihapus');
    }

    public function produk(ProdukModel $produkModel, Kategori $kategori)
    {
        return view('admin.produkModels.createProduk', compact('produkModel', 'kategori'));
    }

    public function storeProduk(Request $request, ProdukModel $produkModel)
    {
        $request->validate([
            'nama' => 'required',
            'status' => 'required|in:0,1',
            'produk_model_id' => 'required|exists:produk_models,id'
        ]);

        Produk::create([
            'nama' => $request->nama,
            'status' => $request->status,
            'produk_model_id' => $request->produk_model_id,
        ]);

        $produkModel = ProdukModel::find($request->produk_model_id);
        return redirect()->route('produkModel.show', ['id' => $produkModel->id, 'kategori' => $produkModel->kategori_id])->with('success', 'Produk berhasil ditambahkan');
    }

    public function editProduk(ProdukModel $produkModel,Produk $produk)
    {
        $kategori = Kategori::find($produkModel->kategori_id);
        $produkModels = ProdukModel::where('kategori_id',$kategori->id)->get();
        return view('admin.produkModels.editProduk', compact('produk', 'produkModel', 'produkModels'));
    }

    public function updateProduk(Request $request, ProdukModel $produkModel, Produk $produk)
    {
        $produk->update(
            [
                'nama' => $request->nama,
                'status' => $request->status,
                'hpp' => $request->hpp,
                'produk_model_id' => $request->produk_model_id,
            ]
        );
        return redirect()->route('produkModel.show', ['id' => $produkModel->id, 'kategori' => $produkModel->kategori_id])->with('success', 'Produk berhasil diperbarui');
    }
}
