<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kasbon;
use App\Models\Freelance;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FreelanceKasbonController extends Controller
{
    public function create(Freelance $freelance)
    {
        $kas = AkunDetail::pluck('nama', 'id')->toArray();
        return view('admin.freelance_kasbons.create', compact('freelance', 'kas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'jumlah' => 'required',
            'keterangan' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            //get kasbon sebelumnya
            $kasbon = Kasbon::where('freelance_id', $request->freelance_id)->orderBy('id', 'DESC')->first();
            if (!empty($kasbon)) {
                $saldoAwal = $kasbon->saldo;
            } else {
                $saldoAwal = 0;
            }

            $data['pemasukan'] = $request->jumlah;
            $data['keterangan'] = $request->keterangan;
            $data['freelance_id'] = $request->freelance_id;
            $data['created_at'] = $request->tanggal;
            $data['saldo'] = $saldoAwal + $request->jumlah;

            //insert kasbon
            Kasbon::create($data);

            //update saldo akun detail
            $akunDetail = AkunDetail::where('id', $request->akun_detail_id)->first();
            $saldo = $akunDetail->saldo;
            $update = $saldo - $request->jumlah;
            $akunDetail->update([
                'saldo' => $update,
            ]);

            //get nama freelance untuk ket kasbon
            $freelance = Freelance::where('id', $request->freelance_id)->first();

            //insert into buku besar table
            BukuBesar::insert([
                'akun_detail_id' => $request->akun_detail_id,
                'ket' => 'kasbon ke ' . $freelance->nama,
                'kredit' => $request->jumlah,
                'debet' => 0,
                'saldo' => $update,
                'created_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('freelances.show', ['freelance' => $request->freelance_id, 'tab' => 'kasbon'])->withSuccess(__('Kasbon created successfully.'));
    }

    public function bayar(Freelance $freelance)
    {
        $kas = AkunDetail::pluck('nama', 'id')->toArray();
        $kasbon = Kasbon::where('freelance_id', $freelance->id)->orderBy('id', 'DESC')->first();
        return view('admin.freelance_kasbons.bayar', compact('freelance', 'kas', 'kasbon'));
    }

    public function bayarStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'jumlah' => 'required',
            'keterangan' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            //get kasbon sebelumnya
            $kasbon = Kasbon::where('freelance_id', $request->freelance_id)->orderBy('id', 'DESC')->first();
            if (!empty($kasbon)) {
                $saldoAwal = $kasbon->saldo;
            } else {
                $saldoAwal = 0;
            }

            $data['pengeluaran'] = $request->jumlah;
            $data['keterangan'] = $request->keterangan;
            $data['freelance_id'] = $request->freelance_id;
            $data['created_at'] = $request->tanggal;
            $data['saldo'] = $saldoAwal - $request->jumlah;

            //insert kasbon
            Kasbon::create($data);

            //update saldo akun detail
            $akunDetail = AkunDetail::where('id', $request->akun_detail_id)->first();
            $saldo = $akunDetail->saldo;
            $update = $saldo + $request->jumlah;
            $akunDetail->update([
                'saldo' => $update,
            ]);

            //get nama freelance untuk ket kasbon
            $freelance = Freelance::where('id', $request->freelance_id)->first();

            //insert into buku besar table
            BukuBesar::insert([
                'akun_detail_id' => $request->akun_detail_id,
                'ket' => 'bayar kasbon dari ' . $freelance->nama,
                'kredit' => 0,
                'debet' => $request->jumlah,
                'saldo' => $update,
                'created_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('freelances.show', ['freelance' => $request->freelance_id, 'tab' => 'kasbon'])->withSuccess(__('Pembayaran kasbon berhasil.'));
    }
}

