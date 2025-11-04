<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Hutang;
use App\Models\Belanja;
use App\Models\BukuBesar;
use App\Models\Freelance;
use App\Models\AkunDetail;
use App\Models\AkunKategori;
use Illuminate\Http\Request;
use App\Models\BelanjaDetail;
use App\Models\FreelanceOvertime;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class FreelanceOvertimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('freelance_overtime_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Ambil filter dari request, default ke bulan dan tahun saat ini
        $bln = $request->get('bulan', date('m'));
        $thn = $request->get('tahun', date('Y'));
        $statusBayar = $request->get('status_bayar', 'all'); // all, sudah, belum
        $nama = $request->get('nama');

        $userId = auth()->user()->id;
        $freelance = Freelance::where('user_id', $userId)->first();

        // Query dasar overtime dengan filter bulan
        $query = FreelanceOvertime::with(['freelance'])
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln);

        if($freelance){
            $query->where('freelance_id', $freelance->id);
        }

        // Filter berdasarkan nama freelance (jika diisi)
        if (!empty($nama)) {
            $query->whereHas('freelance', function ($q) use ($nama) {
                $q->where('nama', 'like', "%$nama%");
            });
        }

        $overtimes = $query->latest()->get();

        // Load hutang untuk setiap overtime dan tentukan status pembayaran
        $overtimes->each(function($overtime) use ($bln, $thn) {
            // Jika status masih pending atau rejected, otomatis belum dibayar
            if ($overtime->status != 'approved') {
                $overtime->status_bayar = 'belum';
                $overtime->hutang = null;
                return;
            }

            // Hanya cek hutang jika status sudah approved
            // Cari hutang lembur untuk freelance dan bulan yang sama
            $hutang = Hutang::with('details')
                ->where('kontak_id', $overtime->freelance_id)
                ->where('jenis', 'lembur')
                ->whereYear('tanggal', $thn)
                ->whereMonth('tanggal', $bln)
                ->first();

            if ($hutang) {
                $totalBayar = $hutang->details->sum('jumlah');
                $sisa = $hutang->jumlah - $totalBayar;
                // Cek apakah sudah lunas (dengan toleransi kecil untuk rounding)
                $overtime->status_bayar = $sisa <= 0.01 ? 'sudah' : 'belum';
                $overtime->hutang = $hutang;
            } else {
                // Approved tapi belum ada hutang (tidak mungkin, tapi tetap ditandai belum)
                $overtime->status_bayar = 'belum';
                $overtime->hutang = null;
            }
        });

        // Filter berdasarkan status pembayaran
        if ($statusBayar !== 'all') {
            $overtimes = $overtimes->filter(function($overtime) use ($statusBayar) {
                return isset($overtime->status_bayar) && $overtime->status_bayar === $statusBayar;
            })->values(); // Reindex collection
        }

        return view('admin.freelance_overtime.index', compact('overtimes', 'bln', 'thn', 'statusBayar', 'nama'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('freelance_overtime_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        $freelance = Freelance::where('user_id', $user->id)->first();
        return view('admin.freelance_overtime.create', compact('user','freelance'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'freelance_id' => 'required|string|max:255',
            'jam_lembur' => 'required|numeric|min:0.5|max:6',
            'keterangan' => 'nullable|string',
        ]);

        $rateLembur = Freelance::find($request->freelance_id)->rate_lembur_per_jam;
        $jumlahUpah = $rateLembur * $request->jam_lembur;

        DB::transaction(function () use ($request, $jumlahUpah) {
            $overtime = FreelanceOvertime::create([
                'freelance_id' => $request->freelance_id,
                'jam_lembur' => $request->jam_lembur,
                'keterangan' => $request->keterangan,
                'jumlah_upah' => $jumlahUpah,
                'status' => 'pending',
                'kategori' => $request->kategori,
            ]);

            // Cari hutang lembur bulan ini yang masih punya sisa (belum lunas)
            $hutangBulanIni = Hutang::with('details')
                ->where('kontak_id', $request->freelance_id)
                ->where('jenis', 'lembur')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->orderBy('tanggal', 'desc')
                ->get();

            $hutang = $hutangBulanIni->first(function ($item) {
                $totalBayar = $item->details->sum('jumlah');
                $sisa = $item->jumlah - $totalBayar;
                return $sisa > 0; // masih ada yang belum lunas
            });

            if ($hutang) {
                // Ada hutang berjalan (belum lunas) di bulan yang sama → tambahkan ke hutang itu
                $hutang->jumlah += $jumlahUpah;
                $hutang->save();
            } else {
                // Tidak ada hutang berjalan (semua lunas atau belum ada) → buat hutang baru
                $hutang = Hutang::create([
                    'kontak_id' => $request->freelance_id,
                    'akun_detail_id' => $request->akun_detail_id,
                    'tanggal' => now()->format('Y-m-d'),
                    'jumlah' => $jumlahUpah,
                    'keterangan' => 'Lembur bulan ' . now()->format('F Y'),
                    'jenis' => 'lembur',
                ]);
            }

            // update overtime hutang id
            $overtime->hutang_id = $hutang->id;
            $overtime->save();

        });

        return redirect()->route('freelance_overtime.index')->with('success', 'Lembur berhasil diajukan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FreelanceOvertime $freelanceOvertime)
    {
        abort_if(Gate::denies('freelance_overtime_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $akunDetails = AkunDetail::with(['akun_kategori'])
            ->whereHas('akun_kategori', function ($q) {
                $q->whereIn('id', [1, 8]); // Kas categories
            })
            ->get();
        $freelance = Freelance::find($freelanceOvertime->freelance_id);
        return view('admin.freelance_overtime.edit', compact('freelanceOvertime', 'freelance','akunDetails'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FreelanceOvertime $freelanceOvertime)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'catatan_akunting' => 'nullable|string',
        ]);

        $freelanceOvertime->update([
            'status' => $request->status,
            'catatan_akunting' => $request->catatan_akunting,
            'overtime_rate_hour' => $request->rate_lembur_per_jam,
            'akun_detail_id' => $request->akun_detail_id,
            'kategori' => $request->kategori,
        ]);

        if($request->status==='approved'){

            $belanja = Belanja::create([
                'nota'          => rand(1000000,100),
                'diskon'        => 0,
                'pembayaran'    => $freelanceOvertime->jumlah_upah,
                'tanggal_beli'  => $freelanceOvertime->updated_at->format('Y-m-d'),
                'total'         => $freelanceOvertime->jumlah_upah,
            ]);

            $akunDetails = AkunDetail::find($request->akun_detail_id);
            $kuranginSaldo = $akunDetails->saldo - $freelanceOvertime->jumlah_upah;
            $akunDetails->update(['saldo' => $kuranginSaldo]);

            if($request->kategori == '456'){
                $kategoriText = 'Lembur Printing';
                $produkId = 679;
            } else {
                $kategoriText = 'Lembur Sublime';
                $produkId = 184;
            }

            $belanjaDetail = BelanjaDetail::create([
                'belanja_id'    => $belanja->id,
                'produk_id'     => $produkId,
                'harga'         => $request->rate_lembur_per_jam,
                'jumlah'        => $freelanceOvertime->jam_lembur,
                'keterangan'    => $kategoriText,
            ]);

            $bukuBesar = BukuBesar::create([
                'akun_detail_id'    => $request->akun_detail_id,
                'ket'               => 'Pembayaran Lembur ke ' . $freelanceOvertime->freelance->nama,
                'kredit'            => $freelanceOvertime->jumlah_upah,
                'debet'             => 0,
                'kode'              => 'blj',
                'detail_id'         => $belanja->id,
            ]);
        }

        return redirect()->route('freelance_overtime.index')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FreelanceOvertime $freelanceOvertime)
    {
        DB::transaction(function () use ($freelanceOvertime) {
            // Simpan data yang diperlukan sebelum delete
            $hutangId = $freelanceOvertime->hutang_id;
            $jumlahUpah = $freelanceOvertime->jumlah_upah;

            // Hapus overtime
            $freelanceOvertime->delete();

            // Update hutang jika masih ada
            if ($hutangId) {
                $hutang = Hutang::with('details')->find($hutangId);

                if ($hutang) {
                    // Cek apakah hutang sudah dibayar (lunas)
                    $totalBayar = $hutang->details->sum('jumlah');
                    $sisaHutang = $hutang->jumlah - $totalBayar;

                    // Kurangi jumlah hutang
                    $hutang->jumlah -= $jumlahUpah;

                    // Pastikan jumlah tidak negatif
                    if ($hutang->jumlah < 0) {
                        $hutang->jumlah = 0;
                    }

                    $hutang->save();
                }
            }
        });

        return redirect()->route('freelance_overtime.index')->with('success', 'Data berhasil dihapus!');
    }
}
