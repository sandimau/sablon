<?php

namespace App\Http\Controllers\Admin;

use App\Models\Hutang;
use App\Models\Kontak;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\HutangDetail;
use App\Models\FreelanceOvertime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HutangController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('hutang.belumLunas', $request->query());
    }

    public function create()
    {
        $kontaks = Kontak::all();
        $jenis = request()->jenis;
        $kas = AkunDetail::kas()->get();

        return view('admin.hutang.create', compact('kontaks', 'jenis', 'kas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kontak_id' => 'required',
            'akun_detail_id' => 'required',
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'jenis' => 'required|in:hutang,piutang',
        ]);

        $hutang = Hutang::create($validated);

        $debet = $validated['jenis'] == 'hutang' ? $validated['jumlah'] : 0;
        $kredit = $validated['jenis'] == 'hutang' ? 0 : $validated['jumlah'];
        $keterangan = $validated['jenis'] == 'hutang' ? 'Hutang dari ' . $hutang->kontak->nama : 'Piutang ke ' . $hutang->kontak->nama;

        BukuBesar::create([
            'akun_detail_id' => $validated['akun_detail_id'],
            'kode' => $validated['jenis'] == 'hutang' ? 'htg' : 'ptg',
            'debet' => $debet,
            'kredit' => $kredit,
            'ket' => $keterangan,
            'detail_id' => $hutang->id,
        ]);

        return redirect()->route('hutang.index')
            ->with('success', request()->jenis == 'hutang' ? 'Hutang berhasil ditambahkan' : 'Piutang berhasil ditambahkan');
    }

    public function bayar(Hutang $hutang)
    {
        $kontaks = Kontak::all();
        $kas = AkunDetail::kas()->get();
        return view('admin.hutang.bayar', compact('hutang', 'kontaks', 'kas'));
    }

    public function bayarStore(Request $request)
    {
        $validated = $request->validate([
            'hutang_id' => 'required',
            'akun_detail_id' => 'required',
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $hutang = Hutang::find($validated['hutang_id']);

        HutangDetail::create($validated);

        // Menentukan jenis transaksi dengan tambahan lembur dan upah
        $jenis = request()->jenis; // hutang, piutang, lembur, upah

        if ($jenis == 'hutang') {
            $kredit = $validated['jumlah'];
            $debet = 0;
            $keterangan = 'Bayar Hutang ke ' . ($hutang->kontak?->nama ?? ($hutang->freelance?->nama ?? '-'));
            $kode = 'htg';
        } elseif ($jenis == 'piutang') {
            $kredit = 0;
            $debet = $validated['jumlah'];
            $keterangan = 'Bayar Piutang dari ' . ($hutang->kontak?->nama ?? ($hutang->freelance?->nama ?? '-'));
            $kode = 'ptg';
        } elseif ($jenis == 'lembur') {
            $kredit = $validated['jumlah'];
            $debet = 0;
            $keterangan = 'Bayar Lembur ke ' . ($hutang->freelance?->nama ?? ($hutang->kontak?->nama ?? '-'));
            $kode = 'lembur';
        } elseif ($jenis == 'upah') {
            $kredit = $validated['jumlah'];
            $debet = 0;
            $keterangan = 'Bayar Upah ke ' . ($hutang->freelance?->nama ?? ($hutang->kontak?->nama ?? '-'));
            $kode = 'upah';
        } else {
            $kredit = 0;
            $debet = 0;
            $keterangan = '';
            $kode = '';
        }

        BukuBesar::create([
            'akun_detail_id' => $validated['akun_detail_id'],
            'kode' => $kode,
            'debet' => $debet,
            'kredit' => $kredit,
            'ket' => $keterangan,
            'detail_id' => $hutang->id,
        ]);

        // Update status overtime menjadi approved jika jenis adalah lembur
        if ($jenis == 'lembur') {
            FreelanceOvertime::where('hutang_id', $hutang->id)
                ->where('status', 'pending')
                ->update(['status' => 'approved']);
        }

        if (request()->jenis == 'hutang') {
            $message = 'Hutang berhasil dibayar';
        } elseif (request()->jenis == 'piutang') {
            $message = 'Piutang berhasil dibayar';
        } elseif (request()->jenis == 'lembur') {
            $message = 'Lembur berhasil dibayar';
        } elseif (request()->jenis == 'upah') {
            $message = 'Upah berhasil dibayar';
        } else {
            $message = 'Transaksi berhasil dibayar';
        }
        return redirect()->route('hutang.index')
            ->with('success', $message);
    }

    public function detail(Hutang $hutang)
    {
        return view('admin.hutang.detail', compact('hutang'));
    }

    public function belumLunas(Request $request)
    {
        $query = Hutang::with(['kontak', 'freelance', 'details'])->latest();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter yang belum lunas (sisa > 0)
        $hutangs = $query->get()->filter(function ($hutang) {
            return $hutang->sisa > 0;
        });

        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 10;
        $hutangs = new \Illuminate\Pagination\LengthAwarePaginator(
            $hutangs->forPage($page, $perPage),
            $hutangs->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $jenisFilter = $request->jenis;

        return view('admin.hutang.belum-lunas', compact('hutangs', 'jenisFilter'));
    }

    public function sudahLunas(Request $request)
    {
        $query = Hutang::with(['kontak', 'freelance', 'details'])->latest();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter yang sudah lunas (sisa <= 0)
        $hutangs = $query->get()->filter(function ($hutang) {
            return $hutang->sisa <= 0;
        });

        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 10;
        $hutangs = new \Illuminate\Pagination\LengthAwarePaginator(
            $hutangs->forPage($page, $perPage),
            $hutangs->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $jenisFilter = $request->jenis;

        return view('admin.hutang.sudah-lunas', compact('hutangs', 'jenisFilter'));
    }
}
