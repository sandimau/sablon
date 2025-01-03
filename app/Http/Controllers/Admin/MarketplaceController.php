<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Carbon\Carbon;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class MarketplaceController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('marketplace_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketplaces = Marketplace::with('kontak', 'kas')->get();

        return view('admin.marketplaces.index', compact('marketplaces'));
    }

    public function show(Marketplace $marketplace)
    {
        $kasMarketplace = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', 'marketplace');
            })
            ->get();
        $kasPenarikan = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', '!=', 'marketplace');
            })
            ->get();
        return view('admin.marketplaces.show', compact('marketplace', 'kasMarketplace', 'kasPenarikan'));
    }

    public function create()
    {
        abort_if(Gate::denies('marketplace_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $kasMarketplace = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', 'marketplace');
            })
            ->get();
        $kasPenarikan = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', '!=', 'marketplace');
            })
            ->get();
        return view('admin.marketplaces.create', compact('kasMarketplace', 'kasPenarikan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'marketplace' => 'required',
            'kas_id' => 'required',
            'penarikan_id' => 'required',
            'kontak_id' => 'required',
        ]);
        Marketplace::create($request->all());

        return redirect()->route('marketplaces.index')->withSuccess(__('Toko created successfully.'));
    }

    public function edit(Marketplace $marketplace)
    {
        abort_if(Gate::denies('marketplace_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $kasMarketplace = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', 'marketplace');
            })
            ->get();
        $kasPenarikan = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', '!=', 'marketplace');
            })
            ->get();
        return view('admin.marketplaces.edit', compact('marketplace','kasMarketplace', 'kasPenarikan'));
    }

    public function update(Request $request, Marketplace $marketplace)
    {
        $marketplace->update($request->all());

        return redirect()->route('marketplaces.index')->withSuccess(__('Toko updated successfully.'));
    }

    public function destroy(Marketplace $marketplace)
    {
        abort_if(Gate::denies('marketplace_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $marketplace->delete();
        return back();
    }

    public function uploadKeuangan(Request $request, Marketplace $id)
    {
        $request->validate([
            'keuangan' => 'required',
        ]);
        DB::transaction(function () use ($request, $id) {
            $file_excel = fopen(request()->keuangan, "r");
            $i = 0;
            $config = $id;
            $marketplace = DB::table('marketplace_formats')->where('jenis', 'keuangan')->where('marketplace', $config->marketplace)->first();

            $header = $marketplace->barisHeader ?? 1;

            $keuangan = [];
            $input = false;
            if ($config->baruKeuangan == 1)
                $input = true;
            else
                //////ambil yg terakhir terinput
                $terakhir = bukuBesar::where('akun_detail_id', $config->kas_id)->latest()->first();

            while (($baris = fgetcsv($file_excel, 1000, ",")) !== false) {

                $i++;
                array_unshift($baris, $i);

                if ($i < $header)
                    continue;
                else if ($i == $header) {

                    if ($baris[1] != $marketplace->kolom1 or $baris[2] != $marketplace->kolom2 or $baris[3] != $marketplace->kolom3)
                        throw new \Exception('excel salah');

                    continue;
                }

                $pattern = '/\.0$/';
                $pattern2 = '/\.00$/';
                $saldo = $baris[$marketplace->saldo];
                $saldo = preg_replace($pattern, '', $saldo);
                $saldo = preg_replace($pattern2, '', $saldo);
                $saldo = str_replace(",", "", $saldo);
                $saldo = $baris[$marketplace->saldo] = str_replace(".", "", $saldo);

                $tanggal = $baris[$marketplace->tanggal];
                $tanggal = $baris[$marketplace->tanggal] = Carbon::createFromFormat($marketplace->formatTanggal, $tanggal)->toDateTimeString();

                $tema = $baris[$marketplace->tema];
                $harga = $baris[$marketplace->harga];
                $harga = preg_replace($pattern, '', $harga);
                $harga = preg_replace($pattern2, '', $harga);
                $harga = str_replace(",", "", $harga);
                $harga = $baris[$marketplace->harga] = str_replace(".", "", $harga);

                if ($i == $header + 1) {
                    /////////ambil tanggal dan saldo terakhir di excel yg diupload
                    $tanggal_terakhir = $tanggal;
                    $saldo_terakhir = $saldo;
                    $ket_terakhir = $tema;
                    $dana_terakhir = $harga;
                }

                ////////jika ketemu dengan tanggal terakhir yg terupload sebelumnya, start mulai input

                if (!$input and $tanggal == $terakhir->created_at and $saldo == $terakhir->saldo) {
                    $input = true;
                    break;
                }

                if ($harga < 0 and strpos($baris[$marketplace->tema], $marketplace->batal) !== false) {
                    $keuangan[] = $baris;
                }
            }

            if ($input) {
                //////////////////////proses masukin dana yg ditarik
                foreach (array_reverse($keuangan) as $baris) {

                    $harga = $baris[$marketplace->harga];
                    $kredit = abs($harga);

                    $tanggal = $baris[$marketplace->tanggal];

                    BukuBesar::create([
                        'akun_detail_id' => $config->penarikan_id,
                        'kode' => 'trf',
                        'created_at' => $tanggal,
                        'detail_id' => 123,
                        'ket' => 'penarikan dari ' . $config->nama,
                        'debet' => $kredit
                    ]);
                }


                $kredit = $debet = 0;
                if ($dana_terakhir < 0)
                    $kredit = abs($dana_terakhir);
                else
                    $debet = $dana_terakhir;

                DB::table('buku_besars')->where('akun_detail_id', $config->kas_id)->delete();

                DB::table('buku_besars')->insert([
                    'akun_detail_id' => $config->kas_id,
                    'kode' => 'byr',
                    'created_at' => $tanggal_terakhir,
                    'detail_id' => 123,
                    'ket' => $ket_terakhir,
                    'debet' => $debet,
                    'kredit' => $kredit,
                    'saldo' => $saldo_terakhir
                ]);

                DB::table('akun_details')->where('id', $config->kas_id)->update(['saldo' => $saldo_terakhir]);

                if ($config->baruKeuangan == 1) {
                    $config->update(['baruKeuangan' => 0]);
                }


                $config->update(['tglUploadKeuangan' => now()]);
            } else
                throw new \Exception('tanggal pengambilan rentangnya kurang panjang');
        });
    }

    public function uploadOrder(Request $request, Marketplace $marketplace)
    {
        $request->validate([
            'order' => 'required',
        ]);
        $extension = $request->file('order')->getClientOriginalExtension();
        if ($extension !== 'xlsx') {
            return back()->withErrors(['order' => 'File harus berformat .xlsx']);
        }
        return redirect()->route('marketplaces.index')->withSuccess(__('Upload order successfully.'));
    }
}
