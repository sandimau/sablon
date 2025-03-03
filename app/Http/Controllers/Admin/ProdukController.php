<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\LastStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class ProdukController extends Controller
{
    public function index(Kategori $kategori)
    {
        $produks = Produk::where('kategori_id',$kategori->id)->get();

        return view('admin.produks.index', compact('produks','kategori'));
    }

    public function create(Kategori $kategori)
    {
        $satuan = [
            'kilo' => 'kilo', 'lembar' => 'lembar', 'rim' => 'rim', 'koli' => 'koli', 'jasa' => 'jasa', 'meter' => 'meter', 'roll' => 'roll', 'gross' => 'gross', 'liter' => 'liter', 'buah' => 'buah',
            'pak(25)' => 'pak(25)', 'pak(50)' => 'pak(50)', 'pak(100)' => 'pak(100)', 'pak(120)' => 'pak(120)', 'pak(200)' => 'pak(200)', 'pak(250)' => 'pak(250)', 'pak(500)' => 'pak(500)', 'pak(1000)' => 'pak(1000)', 'pak(2000)' => 'pak(2000)',
        ];
        return view('admin.produks.create', compact('satuan','kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
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

        Produk::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'satuan' => $request->satuan,
            'deskripsi' => $request->deskripsi,
            'beli' => $request->beli,
            'jual' => $request->jual,
            'stok' => $request->stok,
            'gambar' => $gambar,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('produks.index',$request->kategori_id)->withSuccess(__('Produk created successfully.'));
    }

    public function edit(Produk $produk)
    {
        $listKategori = Kategori::get();
        $satuan = [
            'kilo' => 'kilo', 'lembar' => 'lembar', 'rim' => 'rim', 'koli' => 'koli', 'jasa' => 'jasa', 'meter' => 'meter', 'roll' => 'roll', 'gross' => 'gross', 'liter' => 'liter', 'buah' => 'buah',
            'pak(25)' => 'pak(25)', 'pak(50)' => 'pak(50)', 'pak(100)' => 'pak(100)', 'pak(120)' => 'pak(120)', 'pak(200)' => 'pak(200)', 'pak(250)' => 'pak(250)', 'pak(500)' => 'pak(500)', 'pak(1000)' => 'pak(1000)', 'pak(2000)' => 'pak(2000)',
        ];
        return view('admin.produks.edit', compact('produk','satuan','listKategori'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        $gambar = $produk->gambar;
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

        $produk->update([
            'nama' => $request->nama,
            'harga' => $request->harga ?? 0,
            'satuan' => $request->satuan,
            'deskripsi' => $request->deskripsi,
            'beli' => $request->beli ?? 0,
            'jual' => $request->jual,
            'stok' => $request->stok,
            'gambar' => $gambar,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('produks.index',$produk->kategori_id)->withSuccess(__('Produk updated successfully.'));
    }

    public function aset()
    {
        $asets = DB::table('produk_last_stoks as t')
            ->join(
                DB::raw('(SELECT produk_id FROM produk_last_stoks GROUP BY produk_id) as subquery'),
                't.produk_id',
                '=',
                'subquery.produk_id'
            )
            ->join('produks as p', 'p.id', '=', 't.produk_id')
            ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
            ->select('t.saldo', 'p.harga_beli','p.nama', 'k.nama as namaKategori')
            ->get();
        return view('admin.produks.aset', compact('asets'));
    }

    public function omzet(Request $request)
    {
        // Get selected year, default to current year if not specified
        $selectedYear = $request->input('year', date('Y'));

        // Get all available years for the dropdown
        $years = DB::table('orders')
            ->select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        $asets = DB::table('produk_last_stoks as t')
            ->join(
                DB::raw('(SELECT produk_id FROM produk_last_stoks GROUP BY produk_id) as subquery'),
                't.produk_id',
                '=',
                'subquery.produk_id'
            )
            ->join('produks as p', 'p.id', '=', 't.produk_id')
            ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
            ->select(
                'k.id as kategori_id',
                'k.nama as namaKategori',
                DB::raw('SUM(t.saldo * p.harga) as nilai_aset')
            )
            ->groupBy('k.id', 'k.nama')
            ->orderBy('k.nama')
            ->get();

        // Get categories first
        $categories = DB::table('kategoris as k')
            ->select(
                'k.id as kategori_id',
                'k.nama as namaKategori'
            )
            ->where('k.jual', 1)
            ->orderBy('k.nama')
            ->get();

        // Get omzet data for the selected year
        $omzetData = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->join('produks as p', 'p.id', '=', 'od.produk_id')
            ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
            ->whereYear('o.created_at', $selectedYear)
            ->select(
                'k.id as kategori_id',
                'k.nama as namaKategori',
                DB::raw('MONTH(o.created_at) as bulan'),
                DB::raw('SUM(od.jumlah * od.harga) as omzet')
            )
            ->groupBy('k.id', 'k.nama', DB::raw('MONTH(o.created_at)'))
            ->get();

        // Create complete dataset with all months
        $omzet = collect();
        foreach ($categories as $category) {
            for ($month = 1; $month <= 12; $month++) {
                $monthlyData = $omzetData
                    ->where('kategori_id', $category->kategori_id)
                    ->where('bulan', $month)
                    ->first();

                $omzet->push((object)[
                    'kategori_id' => $category->kategori_id,
                    'namaKategori' => $category->namaKategori,
                    'bulan' => $month,
                    'tahun' => $selectedYear,
                    'omzet' => $monthlyData ? $monthlyData->omzet : 0
                ]);
            }
        }

        return view('admin.produks.omzet', compact('omzet', 'asets', 'years', 'selectedYear'));
    }

    public function omzetDetail(Kategori $kategori, Request $request)
    {
        // Get selected year and month, default to current if not specified
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', date('m'));

        // Get all available years for the dropdown
        $years = DB::table('orders')
            ->select(DB::raw('DISTINCT YEAR(created_at) as year'))
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Get products with their daily sales for the selected month
        $products = DB::table('produks as p')
            ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
            ->leftJoin('produk_last_stoks as pls', 'pls.produk_id', '=', 'p.id')
            ->where('k.id', $kategori->id)
            ->select(
                'p.id',
                'p.nama as varian',
                'k.nama as nama_kategori',
                'pls.saldo as stok'
            )
            ->get();

        // Get daily sales data
        foreach ($products as $product) {
            // Calculate average sales for the last 3 months
            $avgSales = DB::table('order_details as od')
                ->join('orders as o', 'o.id', '=', 'od.order_id')
                ->where('od.produk_id', $product->id)
                ->whereRaw('o.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)')
                ->avg('od.jumlah');

            $product->rata_penjualan = round($avgSales ?: 0, 1);

            // Get daily sales for the selected month
            $dailySales = DB::table('order_details as od')
                ->join('orders as o', 'o.id', '=', 'od.order_id')
                ->where('od.produk_id', $product->id)
                ->whereYear('od.created_at', $selectedYear)
                ->whereMonth('od.created_at', $selectedMonth)
                ->select(
                    DB::raw('DATE(od.created_at) as date'),
                    DB::raw('SUM(od.jumlah) as total_sales'),
                    DB::raw('DAY(od.created_at) as day')
                )
                ->groupBy('date', 'day')
                ->get();

            // Convert ke array dengan day sebagai key
            $product->daily_sales = $dailySales->pluck('total_sales', 'day')->toArray();
        }

        return view('admin.produks.omzetDetail', compact('products', 'kategori', 'years', 'selectedYear', 'selectedMonth'));
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produks.index',$produk->kategori_id)->withDanger(__('Produk deleted successfully.'));
    }
}
