<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\User;
use App\Models\Hutang;
use App\Models\Freelance;
use Illuminate\Http\Request;
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
        $cloudId = '616202024171916';
        // Ambil tanggal dari request jika ada, default H-1 sesuai dokumentasi
        $attendanceUpload = $request->get('tanggal', now()->subDay()->format('Y-m-d'));
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

            // Cek apakah response body menunjukkan success atau error
            if (isset($responseData['success']) && $responseData['success'] === false) {
                $errorMsg = $responseData['msg'] ?? 'Error dari API Fingerspot';

                // Penjelasan error code sesuai dokumentasi Fingerspot.io
                $errorMessages = [
                    'IO_API_ERR_1' => 'Mesin tidak ditemukan. Cloud ID pada perintah URL Get Data Scan tidak ada di akun web Fingerspot.io atau salah ketik Cloud ID.',
                    'IO_API_ERR_2' => 'Akun tidak ditemukan. Pastikan akun Fingerspot.io Anda aktif dan valid.',
                    'IO_API_ERR_3' => 'Parameter {auth} tidak sesuai. Token autentikasi tidak valid. Pastikan Cloud ID, tanggal, waktu, dan API Key sudah benar.',
                    'IO_API_ERR_4' => 'Akun melebihi DUE DATE. Masa aktif akun Fingerspot.io Anda telah berakhir. Perpanjang langganan Anda.',
                    'IO_API_ERR_5' => 'Mesin belum berlangganan API SDK. Aktifkan langganan API SDK untuk mesin absensi ini di akun Fingerspot.io Anda.',
                    'IO_API_ERR_6' => 'Belum berlangganan AddOn API SDK scan GPS. Jika memerlukan data GPS, aktifkan AddOn API SDK scan GPS di akun Fingerspot.io Anda.',
                    'IO_API_ERR_7' => 'Sudah mencapai limit 100 kali request API per hari. Limit harian API telah tercapai. Coba lagi besok atau hubungi support Fingerspot.io untuk meningkatkan limit.',
                ];

                $errorDescription = $errorMessages[$errorMsg] ?? 'Terjadi error saat mengambil data dari Fingerspot.io';

                // Tambahkan informasi debugging khusus untuk error tertentu
                $debugInfo = [];
                if ($errorMsg === 'IO_API_ERR_1') {
                    $debugInfo['cloud_id_digunakan'] = $cloudId;
                    $debugInfo['saran'] = 'Periksa apakah Cloud ID ' . $cloudId . ' sudah terdaftar di akun Fingerspot.io Anda. Pastikan Cloud ID tidak salah ketik.';
                } elseif ($errorMsg === 'IO_API_ERR_3') {
                    $debugInfo['saran'] = 'Periksa kembali: Cloud ID, tanggal (format Y-m-d), dan API Key. Pastikan waktu server tidak terlalu berbeda dengan waktu Fingerspot.io.';
                }

                return response()->json([
                    'success' => false,
                    'error' => $errorMsg,
                    'error_description' => $errorDescription,
                    'tanggal_digunakan' => $attendanceUpload,
                    'debug_info' => $debugInfo,
                    'data' => $responseData,
                ]);
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
}
