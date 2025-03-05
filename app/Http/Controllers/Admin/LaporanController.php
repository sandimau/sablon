<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use App\Models\Order;
use App\Models\Hutang;
use App\Models\Produk;
use App\Models\Belanja;
use App\Models\Tunjangan;
use App\Models\AkunDetail;
use App\Models\Penggajian;
use App\Models\ProdukStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    public function neraca()
    {
        $kas = AkunDetail::TotalKas();
        $modal = AkunDetail::modal();

        $piutang = Hutang::with('details')->where('jenis', '=', 'piutang')->get();
        $hutang = Hutang::with('details')->where('jenis', '=', 'hutang')->get();

        $total_piutang = 0;
        $total_hutang = 0;
        $total_order = 0;
        foreach ($piutang as $item) {
            $total_piutang += $item->sisa;
        }

        foreach ($hutang as $item) {
            $total_hutang += $item->sisa;
        }

        $produk = Produk::all();

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
                DB::raw('SUM(CAST(CAST(t.saldo AS DECIMAL(30,2)) * CAST(p.harga_beli AS DECIMAL(30,2)) AS DECIMAL(30,2))) as total_aset')
            )
            ->groupBy('k.nama')
            ->orderBy('k.nama')
            ->get()
            ->groupBy('namaKategori');

        $totalAllAsets = 0;
        foreach ($asets as $item) {
            $totalAset = $item->sum('total_aset');
            $totalAllAsets += $totalAset;
        }

        return view('admin.laporan.neraca', compact('kas', 'modal', 'total_piutang', 'total_hutang', 'totalAllAsets'));
    }

    public function labarugi()
    {
        $bulan = request('bulan') ?? date('Y-m');
        $pilihan_parts = explode('-', $bulan);
        $thn = $pilihan_parts[0];
        $bln = $pilihan_parts[1];

        $potongan = Order::selectRaw('sum(ongkir-diskon) as total_omzet')->whereYear('created_at', $thn)->whereMonth('created_at', $bln)->first()->total_omzet;

        $penjualan = DB::table('order_details')
            ->selectRaw('sum(jumlah*harga) as total_omzet,sum(hpp * jumlah) as total_hpp')
            ->join('produksis', 'produksi_id', '=', 'produksis.id')
            ->join('orders', 'order_id', '=', 'orders.id')
            ->where('produksis.id', '<>', 9)
            ->whereYear('orders.created_at', $thn)
            ->whereMonth('orders.created_at', $bln)
            ->first();

        $opname = ProdukStok::selectRaw('sum(hpp * COALESCE(tambah,0) - hpp * COALESCE(kurang,0)) as total_opname')
            ->where('kode', 'opn')
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln)
            ->first()->total_opname;

        $beban = DB::table('produks')
            ->selectRaw('sum(belanja_details.harga*jumlah) as total,
            kategoris.nama as kategori,kategori_id')
            ->join('kategoris', 'kategori_id', '=', 'kategoris.id')
            ->join('belanja_details', 'produk_id', '=', 'produks.id')
            ->join('belanjas', 'belanja_details.belanja_id', '=', 'belanjas.id')
            ->whereNull('produks.stok')
            ->whereYear('belanjas.created_at', $thn)
            ->whereMonth('belanjas.created_at', $bln)
            ->first()->total;

        $gaji = Penggajian::selectRaw('sum(total+kasbon) as total_gaji')
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln)
            ->first()->total_gaji;

        $tunjangan = Tunjangan::selectRaw('sum(jumlah) as total_tunjangan')
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln)
            ->first()->total_tunjangan;

        $omzet = $penjualan->total_omzet + $potongan;
        $hpp = $penjualan->total_hpp;

        // Get the earliest order date from database
        $earliest_date = Order::min('created_at');
        $start_date = $earliest_date ? new DateTime($earliest_date) : now();
        $end_date = now();

        // Generate all months from earliest date until now
        $bulan = [];
        $temp_date = clone $start_date;
        while ($temp_date <= $end_date) {
            $key = $temp_date->format('Y-m');
            $bulan[$key] = $temp_date->format('F Y');
            // Menggunakan cara yang lebih aman untuk increment bulan
            $temp_date->setDate(
                (int) $temp_date->format('Y'),
                (int) $temp_date->format('m') + 1,
                1
            );
        }

        // Sort array in reverse chronological order (newest first)
        krsort($bulan);

        // Set default selected month to current month if not specified in request
        $selected_month = request('bulan', date('Y-m'));

        return view('admin.laporan.labarugi', compact('omzet', 'hpp', 'opname', 'beban', 'gaji', 'tunjangan', 'bulan', 'selected_month'));
    }

    public function labaKotor(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');
        $pilihan_parts = explode('-', $bulan);
        $thn = $pilihan_parts[0];
        $bln = $pilihan_parts[1];
        $view_type = $request->view_type ?? 'kategori';

        if ($view_type == 'kategori') {
            // Get data per kategori
            $data = DB::table('order_details as od')
                ->join('orders as o', 'o.id', '=', 'od.order_id')
                ->join('produks as p', 'p.id', '=', 'od.produk_id')
                ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
                ->where('od.produksi_id', '<>', 9)
                ->whereYear('o.created_at', $thn)
                ->whereMonth('o.created_at', $bln)
                ->select(
                    'k.nama as kategori',
                    'k.id as kategori_id',
                    DB::raw('SUM(od.jumlah * od.harga) as omzet'),
                    DB::raw('SUM(p.hpp * od.jumlah) as hpp'),
                    DB::raw('COALESCE((
                        SELECT SUM(ps.hpp * COALESCE(ps.tambah,0) - ps.hpp * COALESCE(ps.kurang,0))
                        FROM produk_stoks ps
                        JOIN produks p2 ON p2.id = ps.produk_id
                        WHERE ps.kode = "opn"
                        AND p2.kategori_id = k.id
                        AND YEAR(ps.created_at) = ' . $thn . '
                        AND MONTH(ps.created_at) = ' . $bln . '
                    ), 0) as opname'),
                    DB::raw('(
                        SUM(od.jumlah * od.harga) -
                        SUM(p.hpp * od.jumlah) +
                        COALESCE((
                            SELECT SUM(ps.hpp * COALESCE(ps.tambah,0) - ps.hpp * COALESCE(ps.kurang,0))
                            FROM produk_stoks ps
                            JOIN produks p2 ON p2.id = ps.produk_id
                            WHERE ps.kode = "opn"
                            AND p2.kategori_id = k.id
                            AND YEAR(ps.created_at) = ' . $thn . '
                            AND MONTH(ps.created_at) = ' . $bln . '
                        ), 0)
                    ) as laba_kotor'),
                    DB::raw('CASE
                        WHEN SUM(od.jumlah * od.harga) > 0
                        THEN ((SUM(od.jumlah * od.harga) - SUM(p.hpp * od.jumlah)) / SUM(od.jumlah * od.harga)) * 100
                        ELSE 0
                    END as persen')
                )
                ->groupBy('k.nama', 'k.id')
                ->orderBy('k.nama')
                ->get();
        } else {
            // Get data per produk
            $data = DB::table('order_details as od')
                ->join('orders as o', 'o.id', '=', 'od.order_id')
                ->join('produks as p', 'p.id', '=', 'od.produk_id')
                ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
                ->where('od.produksi_id', '<>', 9)
                ->whereYear('o.created_at', $thn)
                ->whereMonth('o.created_at', $bln)
                ->select(
                    'k.nama as kategori',
                    'p.nama as produk',
                    DB::raw('SUM(od.jumlah * od.harga) as omzet'),
                    DB::raw('SUM(p.hpp * od.jumlah) as hpp'),
                    DB::raw('COALESCE((
                        SELECT sum(hpp * COALESCE(tambah,0) - hpp * COALESCE(kurang,0))
                        FROM produk_stoks ps
                        WHERE ps.kode = "opn"
                        AND ps.produk_id = p.id
                        AND YEAR(ps.created_at) = ' . $thn . '
                        AND MONTH(ps.created_at) = ' . $bln . '
                    ), 0) as opname'),
                    DB::raw('(
                        SUM(od.jumlah * od.harga) -
                        SUM(p.hpp * od.jumlah) +
                        COALESCE((
                            SELECT sum(hpp * COALESCE(tambah,0) - hpp * COALESCE(kurang,0))
                            FROM produk_stoks ps
                            WHERE ps.kode = "opn"
                            AND ps.produk_id = p.id
                            AND YEAR(ps.created_at) = ' . $thn . '
                            AND MONTH(ps.created_at) = ' . $bln . '
                        ), 0)
                    ) as laba_kotor'),
                    DB::raw('CASE
                        WHEN SUM(od.jumlah * od.harga) > 0
                        THEN ((SUM(od.jumlah * od.harga) - SUM(p.hpp * od.jumlah)) / SUM(od.jumlah * od.harga)) * 100
                        ELSE 0
                    END as persen')
                )
                ->groupBy('k.nama', 'p.nama', 'p.id')
                ->orderBy('k.nama')
                ->orderBy('p.nama')
                ->get();
        }

        // Get the earliest order date from database
        $earliest_date = Order::min('created_at');
        $start_date = $earliest_date ? new DateTime($earliest_date) : now();
        $end_date = now();

        // Generate all months from earliest date until now
        $bulan = [];
        $temp_date = clone $start_date;
        while ($temp_date <= $end_date) {
            $key = $temp_date->format('Y-m');
            $bulan[$key] = $temp_date->format('F Y');
            // Menggunakan cara yang lebih aman untuk increment bulan
            $temp_date->setDate(
                (int) $temp_date->format('Y'),
                (int) $temp_date->format('m') + 1,
                1
            );
        }

        // Sort array in reverse chronological order (newest first)
        krsort($bulan);

        // Set default selected month to current month if not specified in request
        $selected_month = request('bulan', date('Y-m'));

        return view('admin.laporan.labakotor', [
            'data' => $data,
            'bulan' => $bulan,
            'selected_month' => $selected_month,
            'view_type' => $view_type
        ]);
    }

    public function labakotordetail(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');
        $pilihan_parts = explode('-', $bulan);
        $thn = $pilihan_parts[0];
        $bln = $pilihan_parts[1];
        $kategori_id = $request->kategori;

        // Validate kategori_id is present
        if (!$kategori_id) {
            return redirect()->route('laporan.labakotor')
                ->with('error', 'Kategori harus dipilih');
        }

        // Base query starting from products to show all products
        $query = DB::table('produks as p')
            ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
            ->leftJoin('order_details as od', 'od.produk_id', '=', 'p.id')
            ->leftJoin('orders as o', function ($join) use ($thn, $bln) {
                $join->on('o.id', '=', 'od.order_id')
                    ->whereYear('o.created_at', '=', $thn)
                    ->whereMonth('o.created_at', '=', $bln);
            })
            ->where('k.id', $kategori_id)
            ->where(function ($query) {
                $query->where('od.produksi_id', '<>', 4)
                    ->orWhereNull('od.produksi_id');
            });

        $data = $query->select(
            'k.nama as kategori_utama',
            'k.nama as kategori',
            'p.nama as produk',
            'p.id as produk_id',
            DB::raw('COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN od.jumlah * od.harga ELSE 0 END), 0) as omzet'),
            DB::raw('COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN p.hpp * od.jumlah ELSE 0 END), 0) as hpp'),
            DB::raw('COALESCE((
                SELECT sum(hpp * COALESCE(tambah,0) - hpp * COALESCE(kurang,0))
                FROM produk_stoks ps
                WHERE ps.kode = "opn"
                AND ps.produk_id = p.id
                AND YEAR(ps.created_at) = ' . $thn . '
                AND MONTH(ps.created_at) = ' . $bln . '
            ), 0) as opname')
        )
            ->addSelect(
                DB::raw('(
                COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN od.jumlah * od.harga ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN p.hpp * od.jumlah ELSE 0 END), 0) +
                COALESCE((
                    SELECT sum(hpp * COALESCE(tambah,0) - hpp * COALESCE(kurang,0))
                    FROM produk_stoks ps
                    WHERE ps.kode = "opn"
                    AND ps.produk_id = p.id
                    AND YEAR(ps.created_at) = ' . $thn . '
                    AND MONTH(ps.created_at) = ' . $bln . '
                ), 0)
            ) as laba_kotor'),
                DB::raw('CASE
                WHEN COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN od.jumlah * od.harga ELSE 0 END), 0) > 0
                THEN ((COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN od.jumlah * od.harga ELSE 0 END), 0) -
                      COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN p.hpp * od.jumlah ELSE 0 END), 0)) /
                      COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN od.jumlah * od.harga ELSE 0 END), 0)) * 100
                ELSE 0
            END as persen')
            )
            ->groupBy('k.nama', 'p.nama', 'p.id')
            ->orderBy('k.nama')
            ->orderBy('p.nama')
            ->get();

        // Generate months for dropdown
        $bulanList = [];
        $start_date = now()->startOfYear();
        $end_date = now();

        while ($start_date <= $end_date) {
            $key = $start_date->format('Y-m');
            $bulanList[$key] = $start_date->format('F Y');
            $start_date->addMonth();
        }

        return view('admin.laporan.labakotordetail', [
            'data' => $data,
            'bulan' => $bulanList,
            'view_type' => 'produk',
            'selected_kategori' => $kategori_id,
            'selected_bulan' => $bulan
        ]);
    }

    public function tunjangan(Request $request)
    {
        $dari = null;
        $sampai = null;

        if ($request->bulan) {
            $dari = $request->bulan . '-01';
            $sampai = date('Y-m-t', strtotime($request->bulan));
            $tunjangans = Tunjangan::query()
                ->when($dari && $sampai, function ($query) use ($dari, $sampai) {
                    $query->whereBetween('created_at', [$dari, $sampai]);
                })
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai]);
        } else {
            $tunjangans = Tunjangan::orderBy('id', 'desc')->paginate(10);
        }

        return view('admin.laporan.tunjangan', compact('tunjangans', 'dari', 'sampai'));
    }

    public function penggajian(Request $request)
    {
        $dari = null;
        $sampai = null;

        if ($request->bulan) {
            $dari = $request->bulan . '-01';
            $sampai = date('Y-m-t', strtotime($request->bulan));
            $penggajians = Penggajian::query()
                ->when($dari && $sampai, function ($query) use ($dari, $sampai) {
                    $query->whereBetween('created_at', [$dari, $sampai]);
                })
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai]);
        } else {
            $penggajians = Penggajian::orderBy('id', 'desc')->paginate(10);
        }

        return view('admin.laporan.penggajian', compact('penggajians', 'dari', 'sampai'));
    }

    public function operasional(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');
        $pilihan_parts = explode('-', $bulan);
        $thn = $pilihan_parts[0];
        $bln = $pilihan_parts[1];
        $view_type = $request->view_type ?? 'kategori';

        if ($view_type == 'kategori') {
            // Get data per kategori
            $data = DB::table('belanja_details as bd')
                ->join('belanjas as b', 'b.id', '=', 'bd.belanja_id')
                ->join('produks as p', 'p.id', '=', 'bd.produk_id')
                ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
                ->whereYear('b.created_at', $thn)
                ->whereMonth('b.created_at', $bln)
                ->whereNull('p.stok')
                ->select(
                    'k.nama as kategori',
                    'k.id as kategori_id',
                    DB::raw('SUM(bd.jumlah * bd.harga) as total_belanja')
                )
                ->groupBy('k.nama', 'k.id')
                ->orderBy('k.nama')
                ->get();
        } else {
            // Get data per produk
            $data = DB::table('belanja_details as bd')
                ->join('belanjas as b', 'b.id', '=', 'bd.belanja_id')
                ->join('produks as p', 'p.id', '=', 'bd.produk_id')
                ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
                ->whereYear('b.created_at', $thn)
                ->whereMonth('b.created_at', $bln)
                ->whereNull('p.stok')
                ->select(
                    'k.nama as kategori',
                    'p.nama as produk',
                    DB::raw('SUM(bd.jumlah * bd.harga) as total_belanja')
                )
                ->groupBy('k.nama', 'p.nama', 'p.id')
                ->orderBy('k.nama')
                ->orderBy('p.nama')
                ->get();
        }

        // Generate months for dropdown
        $bulanList = [];
        $start_date = now()->startOfYear();
        $end_date = now();

        while ($start_date <= $end_date) {
            $key = $start_date->format('Y-m');
            $bulanList[$key] = $start_date->format('F Y');
            $start_date->addMonth();
        }

        return view('admin.laporan.operasional', [
            'data' => $data,
            'bulan' => $bulanList,
            'view_type' => $view_type
        ]);
    }

    public function operasionaldetail(Request $request)
    {
        $bulan = $request->bulan ?? date('Y-m');
        $pilihan_parts = explode('-', $bulan);
        $thn = $pilihan_parts[0];
        $bln = $pilihan_parts[1];
        $kategori_id = $request->kategori;

        // Validate kategori_id is present
        if (!$kategori_id) {
            return redirect()->route('laporan.operasional')
                ->with('error', 'Kategori harus dipilih');
        }

        // Base query starting from products to show all products
        $query = DB::table('produks as p')
            ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
            ->leftJoin('belanja_details as bd', 'bd.produk_id', '=', 'p.id')
            ->leftJoin('belanjas as b', function ($join) use ($thn, $bln) {
                $join->on('b.id', '=', 'bd.belanja_id')
                    ->whereYear('b.created_at', '=', $thn)
                    ->whereMonth('b.created_at', '=', $bln);
            })
            ->whereNull('p.stok')
            ->where('k.id', $kategori_id);

        $data = $query->select(
            'k.nama as kategori',
            'p.nama as produk',
            'p.id as produk_id',
            DB::raw('COALESCE(SUM(CASE WHEN b.id IS NOT NULL THEN bd.jumlah * bd.harga ELSE 0 END), 0) as total_belanja')
        )
            ->groupBy('k.nama', 'p.nama', 'p.id')
            ->having('total_belanja', '>', 0)
            ->orderBy('k.nama')
            ->orderBy('p.nama')
            ->get();

        // Generate months for dropdown
        $bulanList = [];
        $start_date = now()->startOfYear();
        $end_date = now();

        while ($start_date <= $end_date) {
            $key = $start_date->format('Y-m');
            $bulanList[$key] = $start_date->format('F Y');
            $start_date->addMonth();
        }

        return view('admin.laporan.operasionaldetail', [
            'data' => $data,
            'bulan' => $bulanList,
            'selected_kategori' => $kategori_id,
            'selected_bulan' => $bulan
        ]);
    }
}
