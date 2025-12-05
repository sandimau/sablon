<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\User;
use App\Models\Hutang;
use App\Models\HutangDetail;
use App\Models\Absensi;
use App\Models\Freelance;
use App\Models\Kasbon;
use App\Models\FreelanceOvertime;
use App\Models\AkunDetail;
use App\Models\BukuBesar;
use App\Models\PembayaranFreelance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class FreelanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('freelance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $freelances = Freelance::all();

        // Tambahkan data untuk setiap freelance
        $freelances->each(function($freelance) {
            // Total upah belum dibayar bulan ini
            $hutangUpah = Hutang::where('jenis', 'upah')
                ->where('kontak_id', $freelance->id)
                ->with('details')
                ->get();
            $totalUpah = $hutangUpah->sum('jumlah');
            $totalBayarUpah = $hutangUpah->sum(function($h) { return $h->details->sum('jumlah'); });
            $freelance->upah_belum_dibayar = $totalUpah - $totalBayarUpah;

            // Total lembur belum dibayar bulan ini
            $hutangLembur = Hutang::where('jenis', 'lembur')
                ->where('kontak_id', $freelance->id)
                ->with('details')
                ->get();
            $totalLembur = $hutangLembur->sum('jumlah');
            $totalBayarLembur = $hutangLembur->sum(function($h) { return $h->details->sum('jumlah'); });
            $freelance->lembur_belum_dibayar = $totalLembur - $totalBayarLembur;

            // Total kehadiran bulan sekarang
            $freelance->total_kehadiran = Absensi::where('name', 'like', '%' . $freelance->nama . '%')
                ->whereYear('tanggal', now()->year)
                ->whereMonth('tanggal', now()->month)
                ->count();

            // Total kasbon belum lunas
            $kasbonTerakhir = Kasbon::where('freelance_id', $freelance->id)
                ->orderBy('id', 'DESC')
                ->first();
            $freelance->kasbon_belum_lunas = $kasbonTerakhir ? $kasbonTerakhir->saldo : 0;
        });

        return view('admin.freelances.index', compact('freelances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_if(Gate::denies('freelance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::all();
        return view('admin.freelances.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $freelance = Freelance::create($request->all());
        return redirect()->route('freelances.index')->with(['success'=>'Data telah berhasil disimpan.']);
    }

    public function keuangan(Request $request)
    {
        // Ambil tahun dan bulan dari request, default ke bulan dan tahun saat ini
        $thn = $request->get('tahun', date('Y'));
        $bln = $request->get('bulan', date('m'));
        $kontak_id = $request->get('kontak_id');
        $jenis_filter = $request->get('jenis');

        // Ambil semua tagihan upah dan lembur untuk bulan yang dipilih
        $query = Hutang::with(['freelance', 'kontak', 'details'])
            ->whereIn('jenis', ['lembur', 'upah'])
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln);

        // Filter by jenis jika dipilih
        if ($jenis_filter && in_array($jenis_filter, ['lembur', 'upah'])) {
            $query->where('jenis', $jenis_filter);
        }

        // Filter by kontak_id jika dipilih
        if ($kontak_id) {
            // Parse format: freelance_X atau kontak_X
            if (strpos($kontak_id, 'freelance_') === 0) {
                $id = str_replace('freelance_', '', $kontak_id);
                $query->where('kontak_id', $id);
            } elseif (strpos($kontak_id, 'kontak_') === 0) {
                $id = str_replace('kontak_', '', $kontak_id);
                $query->where('kontak_id', $id);
            }
        }

        $tagihans = $query->orderBy('tanggal', 'desc')->get();

        // Ambil daftar freelance/kontak yang memiliki tagihan untuk dropdown filter
        $freelances = Hutang::whereIn('jenis', ['lembur', 'upah'])
            ->whereNotNull('kontak_id')
            ->with('freelance')
            ->get()
            ->pluck('freelance')
            ->filter()
            ->unique('id')
            ->map(function($item) {
                return [
                    'id' => 'freelance_' . $item->id,
                    'nama' => $item->nama,
                    'type' => 'freelance',
                    'original_id' => $item->id
                ];
            })
            ->sortBy('nama')
            ->values();

        // Gabungkan freelance dan kontak untuk dropdown
        $all_freelances = $freelances->sortBy('nama')->values();

        // Hitung total tagihan
        $total_tagihan = $tagihans->sum('jumlah');

        // Hitung total yang sudah dibayar
        $total_bayar = $tagihans->sum(function($tagihan) {
            return $tagihan->details->sum('jumlah');
        });

        // Hitung total sisa
        $total_sisa = $total_tagihan - $total_bayar;

        return view('admin.freelances.keuangan', compact('tagihans', 'thn', 'bln', 'total_tagihan', 'total_bayar', 'total_sisa', 'all_freelances', 'kontak_id', 'jenis_filter'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Freelance $freelance)
    {
        abort_if(Gate::denies('freelance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Ambil tahun dan bulan dari request, default ke bulan dan tahun saat ini
        $thn = $request->get('tahun', date('Y'));
        $bln = $request->get('bulan', date('m'));
        $tab = $request->get('tab', 'profil');

        // Ambil data hutang upah freelance ini
        $hutangUpah = Hutang::where('jenis', 'upah')
            ->where('kontak_id', $freelance->id)
            ->with('details')
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil data hutang lembur freelance ini
        $hutangLembur = Hutang::where('jenis', 'lembur')
            ->where('kontak_id', $freelance->id)
            ->with('details')
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil data kehadiran freelance ini
        $absensis = Absensi::where('name', 'like', '%' . $freelance->nama . '%')
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil data kasbon freelance ini
        $kasbons = Kasbon::where('freelance_id', $freelance->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil data pembayaran freelance untuk bulan ini
        $pembayaran = PembayaranFreelance::where('freelance_id', $freelance->id)
            ->where('tahun', $thn)
            ->where('bulan', $bln)
            ->with('akunDetail')
            ->first();

        // Ambil semua riwayat pembayaran freelance ini
        $riwayatPembayaran = PembayaranFreelance::where('freelance_id', $freelance->id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->with('akunDetail')
            ->get();

        // Hitung total
        $totalUpah = $hutangUpah->sum('jumlah');
        $totalLembur = $hutangLembur->sum('jumlah');
        $totalBayarUpah = $hutangUpah->sum(function($h) { return $h->details->sum('jumlah'); });
        $totalBayarLembur = $hutangLembur->sum(function($h) { return $h->details->sum('jumlah'); });

        // Hitung sisa yang belum dibayar
        $sisaUpah = $totalUpah - $totalBayarUpah;
        $sisaLembur = $totalLembur - $totalBayarLembur;

        // Ambil hutang upah dan lembur yang belum lunas untuk tombol bayar
        $hutangUpahBelumLunas = $hutangUpah->filter(function($h) {
            return ($h->jumlah - $h->details->sum('jumlah')) > 0;
        });
        $hutangLemburBelumLunas = $hutangLembur->filter(function($h) {
            return ($h->jumlah - $h->details->sum('jumlah')) > 0;
        });

        // Hitung saldo kasbon terakhir
        $saldoKasbon = $kasbons->first() ? $kasbons->first()->saldo : 0;

        // Cek apakah bulan ini sudah ada pembayaran (untuk print slip)
        $sudahDibayar = ($totalBayarUpah > 0 || $totalBayarLembur > 0);

        return view('admin.freelances.show', compact(
            'freelance',
            'hutangUpah',
            'hutangLembur',
            'absensis',
            'kasbons',
            'thn',
            'bln',
            'tab',
            'totalUpah',
            'totalLembur',
            'totalBayarUpah',
            'totalBayarLembur',
            'sisaUpah',
            'sisaLembur',
            'hutangUpahBelumLunas',
            'hutangLemburBelumLunas',
            'saldoKasbon',
            'sudahDibayar',
            'pembayaran',
            'riwayatPembayaran'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Freelance $freelance)
    {
        abort_if(Gate::denies('freelance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::all();
        return view('admin.freelances.edit', compact('freelance','users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Freelance $freelance)
    {
        $freelance->update($request->all());
        return redirect()->route('freelances.index')->with(['success'=>'Data telah berhasil diubah.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Freelance $freelance)
    {
        $freelance->delete();
        return back();
    }

    public function tarikData(Request $request)
    {
        $cloudId = 'C2638898030E2729';
        // Ambil tanggal dari request jika ada, default H-1 sesuai dokumentasi
        $attendanceUpload = $request->get('tanggal', now()->format('Y-m-d'));
        $currentTime = now()->format('YmdHis');
        $apiKey = 'YQKLVZL51DU1TSLD';

        // Validasi format tanggal (Y-m-d)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $attendanceUpload)) {
            return response()->json([
                'success' => false,
                'error' => 'Format tanggal tidak valid. Gunakan format Y-m-d (contoh: 2024-01-15)',
            ], 400);
        }

        // Validasi tanggal tidak boleh di masa depan
        $tanggalInput = \Carbon\Carbon::parse($attendanceUpload)->startOfDay();
        $hariIni = now()->startOfDay();
        $kemarin = now()->subDay()->startOfDay();

        if ($tanggalInput->gt($hariIni)) {
            return response()->json([
                'success' => false,
                'error' => 'Tanggal tidak boleh di masa depan',
                'error_description' => 'Tanggal yang digunakan (' . $attendanceUpload . ') adalah tanggal di masa depan. Gunakan tanggal hari ini atau sebelumnya.',
                'tanggal_digunakan' => $attendanceUpload,
                'saran_tanggal' => $kemarin->format('Y-m-d') . ' (H-1)',
            ], 400);
        }

        // Validasi tanggal tidak terlalu lama di masa lalu (maksimal 1 tahun)
        $satuTahunLalu = now()->subYear()->startOfDay();
        if ($tanggalInput->lt($satuTahunLalu)) {
            return response()->json([
                'success' => false,
                'error' => 'Tanggal terlalu lama di masa lalu',
                'error_description' => 'Tanggal yang digunakan terlalu lama di masa lalu. Data absensi biasanya tersedia untuk 1 tahun terakhir.',
                'tanggal_digunakan' => $attendanceUpload,
                'saran_tanggal' => $kemarin->format('Y-m-d') . ' (H-1)',
            ], 400);
        }

        // Buat auth token sesuai dokumentasi: md5(Cloud_ID + attendance_upload + current_time + API_Key)
        $auth = md5($cloudId . $attendanceUpload . $currentTime . $apiKey);

        // Buat URL GET sesuai format dokumentasi
        // Format: /api/download/attendance_log/{Cloud_ID}/{attendance_upload}/{format_date}/{property}/{direction}/{export_type}/{auth}/{current_time}
        $url = "https://api.fingerspot.io/api/download/attendance_log/{$cloudId}/{$attendanceUpload}/6/date_time/asc/json/{$auth}/{$currentTime}";

        // Panggil API dengan timeout
        $response = Http::timeout(30)->get($url);

        if ($response->successful()) {
            $responseData = $response->json();

            // Jika "success" true, masukkan data ke table
            if (isset($responseData['success']) && $responseData['success'] === true && isset($responseData['data']) && is_array($responseData['data'])) {
                foreach ($responseData['data'] as $attendance) {
                    // Asumsikan key dan struktur kolom:
                    // Silakan sesuaikan kolom berikut sesuai dengan kebutuhan dan struktur tabel di database Anda
                    DB::table('absensis')->insert([
                        'nik'            => $attendance['NIK'] ?? null,
                        'name'            => $attendance['Name'] ?? null,
                        'tanggal'       => $attendance['Date Time'] ?? null,
                        'type'       => $attendance['Type'] ?? null,
                    ]);

                    $freelance = Freelance::where('nama', 'like', '%' . $attendance['Name'] . '%')->first();
                    if ($freelance) {
                        // Cari hutang upah bulan ini yang masih punya sisa (belum lunas)
                        $hutangBulanIni = Hutang::with('details')
                            ->where('kontak_id', $freelance->id)
                            ->where('jenis', 'upah')
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
                            $hutang->jumlah += $freelance->upah;
                            $hutang->save();
                        } else {
                            // Tidak ada hutang berjalan (semua lunas atau belum ada) → buat hutang baru
                            $hutang = Hutang::create([
                                'kontak_id' => $freelance->id,
                                'akun_detail_id' => $freelance->akun_detail_id,
                                'tanggal' => now()->format('Y-m-d'),
                                'jumlah' => $freelance->upah,
                                'keterangan' => 'Upah bulan ' . now()->format('F Y'),
                                'jenis' => 'upah',
                            ]);
                        }
                    }

                }
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'tanggal_digunakan' => $attendanceUpload,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'HTTP Error: ' . $response->status(),
                'error_message' => $response->body(),
                'status_code' => $response->status(),
            ], $response->status());
        }
    }

    public function upah(Request $request)
    {
        // Ambil tahun dan bulan dari request, default ke bulan dan tahun saat ini
        $thn = $request->get('tahun', date('Y'));
        $bln = $request->get('bulan', date('m'));
        $nama = $request->get('nama');
        $statusBayar = $request->get('status_bayar', 'all');

        // Query dasar untuk hutang jenis upah
        $query = Hutang::where('jenis', 'upah')
            ->with(['freelance', 'details']);

        // Filter by bulan dan tahun
        $query->whereYear('tanggal', $thn)
              ->whereMonth('tanggal', $bln);

        // Filter by nama freelance jika ada
        if ($nama) {
            $query->whereHas('freelance', function($q) use ($nama) {
                $q->where('nama', 'like', '%' . $nama . '%');
            });
        }

        // Get semua hutang dulu untuk filter status_bayar
        $hutang = $query->get();

        // Filter by status pembayaran jika dipilih
        if ($statusBayar != 'all') {
            $hutang = $hutang->filter(function($item) use ($statusBayar) {
                $sisa = $item->sisa;
                if ($statusBayar == 'sudah') {
                    return $sisa <= 0;
                } elseif ($statusBayar == 'belum') {
                    return $sisa > 0;
                }
                return true;
            });
        }

        return view('admin.freelances.upah', compact('hutang', 'thn', 'bln', 'nama', 'statusBayar'));
    }

    public function kehadiran()
    {
        $absensis = Absensi::all();
        $freelances = Freelance::all();

        // Buat array untuk menyimpan hasil pengecekan
        $dataAbsensi = $absensis->map(function($absen) use ($freelances) {
            // Normalisasi nama dgn lower trim dan hilangkan spasi agar 'prayoga dwiputra' match 'Prayoga Dwi Putra'
            $absenNama = preg_replace('/\s+/', '', strtolower(trim($absen->name)));
            $match = $freelances->first(function($freelance) use ($absenNama) {
                $freelanceNama = preg_replace('/\s+/', '', strtolower(trim($freelance->nama)));
                return $freelanceNama == $absenNama;
            });

            return [
                'absensi' => $absen,
                'found_in_freelance' => $match ? true : false,
                'freelance_data' => $match,
            ];
        });

        return view('admin.freelances.kehadiran', compact('dataAbsensi'));
    }

    /**
     * Menampilkan slip pembayaran freelance per bulan
     */
    public function slip(Request $request, Freelance $freelance)
    {
        abort_if(Gate::denies('freelance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $thn = $request->get('tahun', date('Y'));
        $bln = $request->get('bulan', date('m'));

        // Ambil data hutang upah freelance ini
        $hutangUpah = Hutang::where('jenis', 'upah')
            ->where('kontak_id', $freelance->id)
            ->with('details')
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil data hutang lembur freelance ini
        $hutangLembur = Hutang::where('jenis', 'lembur')
            ->where('kontak_id', $freelance->id)
            ->with('details')
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Ambil data kehadiran freelance ini
        $absensis = Absensi::where('name', 'like', '%' . $freelance->nama . '%')
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal', 'asc')
            ->get();

        // Hitung total jam lembur dari tabel freelance_overtimes
        $totalJamLembur = FreelanceOvertime::where('freelance_id', $freelance->id)
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln)
            ->sum('jam_lembur');

        // Hitung jumlah hari kerja (kehadiran)
        $jumlahHariKerja = $absensis->count();

        // Hitung total
        $totalUpah = $hutangUpah->sum('jumlah');
        $totalLembur = $hutangLembur->sum('jumlah');
        $totalBayarUpah = $hutangUpah->sum(function($h) { return $h->details->sum('jumlah'); });
        $totalBayarLembur = $hutangLembur->sum(function($h) { return $h->details->sum('jumlah'); });

        // Total keseluruhan
        $totalPendapatan = $totalUpah + $totalLembur;
        $totalDibayar = $totalBayarUpah + $totalBayarLembur;

        // Ambil potongan kasbon bulan ini (jika ada)
        $kasbonBulanIni = Kasbon::where('freelance_id', $freelance->id)
            ->whereYear('created_at', $thn)
            ->whereMonth('created_at', $bln)
            ->where('pengeluaran', '>', 0)
            ->sum('pengeluaran');

        // Total bersih yang diterima
        $totalBersih = $totalDibayar - $kasbonBulanIni;

        // Nama bulan
        $namaBulan = \Carbon\Carbon::create()->month($bln)->translatedFormat('F');

        return view('admin.freelances.slip', compact(
            'freelance',
            'hutangUpah',
            'hutangLembur',
            'absensis',
            'thn',
            'bln',
            'namaBulan',
            'totalUpah',
            'totalLembur',
            'totalBayarUpah',
            'totalBayarLembur',
            'totalPendapatan',
            'totalDibayar',
            'kasbonBulanIni',
            'totalBersih',
            'totalJamLembur',
            'jumlahHariKerja'
        ));
    }

    /**
     * Form bayar semua (upah + lembur - kasbon)
     */
    public function bayarSemua(Request $request, Freelance $freelance)
    {
        abort_if(Gate::denies('freelance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $thn = $request->get('tahun', date('Y'));
        $bln = $request->get('bulan', date('m'));

        // Ambil data hutang upah yang belum lunas
        $hutangUpah = Hutang::where('jenis', 'upah')
            ->where('kontak_id', $freelance->id)
            ->with('details')
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln)
            ->get()
            ->filter(function($h) {
                return ($h->jumlah - $h->details->sum('jumlah')) > 0;
            });

        // Ambil data hutang lembur yang belum lunas
        $hutangLembur = Hutang::where('jenis', 'lembur')
            ->where('kontak_id', $freelance->id)
            ->with('details')
            ->whereYear('tanggal', $thn)
            ->whereMonth('tanggal', $bln)
            ->get()
            ->filter(function($h) {
                return ($h->jumlah - $h->details->sum('jumlah')) > 0;
            });

        // Hitung sisa upah dan lembur
        $sisaUpah = $hutangUpah->sum(function($h) {
            return $h->jumlah - $h->details->sum('jumlah');
        });
        $sisaLembur = $hutangLembur->sum(function($h) {
            return $h->jumlah - $h->details->sum('jumlah');
        });

        // Ambil saldo kasbon terakhir
        $kasbonTerakhir = Kasbon::where('freelance_id', $freelance->id)
            ->orderBy('id', 'DESC')
            ->first();
        $saldoKasbon = $kasbonTerakhir ? $kasbonTerakhir->saldo : 0;

        // Total sebelum potongan
        $totalSebelumPotongan = $sisaUpah + $sisaLembur;

        // Total setelah potongan kasbon
        $potonganKasbon = min($saldoKasbon, $totalSebelumPotongan);
        $totalDiterima = $totalSebelumPotongan - $potonganKasbon;

        // Ambil daftar kas
        $kas = AkunDetail::pluck('nama', 'id')->toArray();

        // Nama bulan
        $namaBulan = Carbon::create()->month($bln)->translatedFormat('F');

        return view('admin.freelances.bayar_semua', compact(
            'freelance',
            'hutangUpah',
            'hutangLembur',
            'sisaUpah',
            'sisaLembur',
            'saldoKasbon',
            'potonganKasbon',
            'totalSebelumPotongan',
            'totalDiterima',
            'kas',
            'thn',
            'bln',
            'namaBulan'
        ));
    }

    /**
     * Proses bayar semua (upah + lembur - kasbon)
     */
    public function bayarSemuaStore(Request $request, Freelance $freelance)
    {
        $request->validate([
            'akun_detail_id' => 'required',
            'tanggal' => 'required|date',
        ]);

        $thn = $request->tahun;
        $bln = $request->bulan;

        DB::transaction(function () use ($request, $freelance, $thn, $bln) {
            $tanggal = $request->tanggal;
            $akunDetailId = $request->akun_detail_id;

            // Ambil hutang upah yang belum lunas
            $hutangUpah = Hutang::where('jenis', 'upah')
                ->where('kontak_id', $freelance->id)
                ->with('details')
                ->whereYear('tanggal', $thn)
                ->whereMonth('tanggal', $bln)
                ->get()
                ->filter(function($h) {
                    return ($h->jumlah - $h->details->sum('jumlah')) > 0;
                });

            // Ambil hutang lembur yang belum lunas
            $hutangLembur = Hutang::where('jenis', 'lembur')
                ->where('kontak_id', $freelance->id)
                ->with('details')
                ->whereYear('tanggal', $thn)
                ->whereMonth('tanggal', $bln)
                ->get()
                ->filter(function($h) {
                    return ($h->jumlah - $h->details->sum('jumlah')) > 0;
                });

            $totalBayarUpah = 0;
            $totalBayarLembur = 0;

            // Bayar semua hutang upah
            foreach ($hutangUpah as $hutang) {
                $sisa = $hutang->jumlah - $hutang->details->sum('jumlah');
                if ($sisa > 0) {
                    HutangDetail::create([
                        'hutang_id' => $hutang->id,
                        'tanggal' => $tanggal,
                        'jumlah' => $sisa,
                        'keterangan' => 'Pembayaran upah bulan ' . Carbon::create()->month($bln)->translatedFormat('F') . ' ' . $thn,
                        'akun_detail_id' => $akunDetailId,
                    ]);
                    $totalBayarUpah += $sisa;
                }
            }

            // Bayar semua hutang lembur
            foreach ($hutangLembur as $hutang) {
                $sisa = $hutang->jumlah - $hutang->details->sum('jumlah');
                if ($sisa > 0) {
                    HutangDetail::create([
                        'hutang_id' => $hutang->id,
                        'tanggal' => $tanggal,
                        'jumlah' => $sisa,
                        'keterangan' => 'Pembayaran lembur bulan ' . Carbon::create()->month($bln)->translatedFormat('F') . ' ' . $thn,
                        'akun_detail_id' => $akunDetailId,
                    ]);
                    $totalBayarLembur += $sisa;
                }
            }

            $totalPembayaran = $totalBayarUpah + $totalBayarLembur;

            // Potong kasbon jika ada dan diminta
            $potonganKasbon = $request->potongan_kasbon ?? 0;
            if ($potonganKasbon > 0) {
                // Ambil saldo kasbon terakhir
                $kasbonTerakhir = Kasbon::where('freelance_id', $freelance->id)
                    ->orderBy('id', 'DESC')
                    ->first();
                $saldoKasbon = $kasbonTerakhir ? $kasbonTerakhir->saldo : 0;

                if ($saldoKasbon > 0 && $potonganKasbon <= $saldoKasbon) {
                    // Insert kasbon pembayaran
                    Kasbon::create([
                        'freelance_id' => $freelance->id,
                        'pengeluaran' => $potonganKasbon,
                        'pemasukan' => 0,
                        'saldo' => $saldoKasbon - $potonganKasbon,
                        'keterangan' => 'Potongan kasbon dari gaji bulan ' . Carbon::create()->month($bln)->translatedFormat('F') . ' ' . $thn,
                        'created_at' => $tanggal,
                    ]);
                }
            }

            // Update saldo akun detail (kas keluar)
            $totalKeluar = $totalPembayaran - $potonganKasbon;
            if ($totalKeluar > 0) {
                $akunDetail = AkunDetail::find($akunDetailId);
                if ($akunDetail) {
                    $saldoBaru = $akunDetail->saldo - $totalKeluar;
                    $akunDetail->update(['saldo' => $saldoBaru]);

                    // Insert buku besar
                    BukuBesar::create([
                        'akun_detail_id' => $akunDetailId,
                        'ket' => 'Pembayaran gaji freelance ' . $freelance->nama . ' bulan ' . Carbon::create()->month($bln)->translatedFormat('F') . ' ' . $thn,
                        'kredit' => $totalKeluar,
                        'debet' => 0,
                        'saldo' => $saldoBaru,
                        'created_at' => $tanggal,
                    ]);
                }
            }

            // Simpan data pembayaran ke tabel baru
            PembayaranFreelance::create([
                'freelance_id' => $freelance->id,
                'akun_detail_id' => $akunDetailId,
                'tanggal' => $tanggal,
                'tahun' => $thn,
                'bulan' => $bln,
                'total_upah' => $totalBayarUpah,
                'total_lembur' => $totalBayarLembur,
                'total_pembayaran' => $totalPembayaran,
                'potongan_kasbon' => $potonganKasbon,
                'total_keluar' => $totalKeluar,
                'keterangan' => 'Pembayaran gaji bulan ' . Carbon::create()->month($bln)->translatedFormat('F') . ' ' . $thn,
                'created_at' => $tanggal,
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('freelances.show', ['freelance' => $freelance->id, 'tab' => 'profil', 'bulan' => $bln, 'tahun' => $thn])
            ->withSuccess('Pembayaran berhasil dilakukan.');
    }

    public function printPembayaran($id)
    {
        $pembayaran = PembayaranFreelance::with(['freelance', 'akunDetail'])->findOrFail($id);

        return view('admin.freelances.print_pembayaran', compact('pembayaran'));
    }

}
