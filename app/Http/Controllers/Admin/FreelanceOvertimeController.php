<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreelanceOvertime;
use App\Models\Freelance;
use App\Models\Belanja;
use App\Models\BelanjaDetail;
use App\Models\AkunKategori;
use App\Models\AkunDetail;
use App\Models\BukuBesar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class FreelanceOvertimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(Gate::denies('freelance_overtime_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userId = auth()->user()->id;
        $freelance = Freelance::where('user_id', $userId)->first();

        if($freelance){
            $overtimes = FreelanceOvertime::where('freelance_id', $freelance->id)->latest()->get();
        } else {
            $overtimes = FreelanceOvertime::latest()->get();
        }

        return view('admin.freelance_overtime.index', compact('overtimes'));
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

        FreelanceOvertime::create([
            'freelance_id' => $request->freelance_id,
            'jam_lembur' => $request->jam_lembur,
            'keterangan' => $request->keterangan,
            'jumlah_upah' => $jumlahUpah,
            'status' => 'pending',
        ]);

        return redirect()->route('freelance_overtime.index')->with('success', 'Lembur berhasil diajukan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(FreelanceOvertime $freelanceOvertime)
    {
        //
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
            $setupLembur = SetupLembur::first();

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
            } else {
                $kategoriText = 'Lembur Sublime';
            }

            $belanjaDetail = BelanjaDetail::create([
                'belanja_id'    => $belanja->id,
                'produk_id'     => $request->kategori,
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
        $freelanceOvertime->delete();
        return redirect()->route('freelance_overtime.index')->with('success', 'Data berhasil dihapus!');
    }
}
