<?php

namespace App\Tafio\bisnis\src\Halaman\marketplace;

use Tafio\core\src\Library\Resource;
use Tafio\core\src\Library\Halaman\crud;
use App\Tafio\bisnis\src\Library\marketplaceProses;
use App\Tafio\bisnis\src\Models\marketplaceConfig;
use Tafio\core\src\Library\Field\file;
use Tafio\core\src\Library\Field\statis;

use Session;

class marketplace_upload extends Resource
{
    use marketplaceProses;
    public function config()
    {

        $config = marketplaceConfig::find(ambil('marketplace'));

        $pesanKeuangan = 'upload terakhir: ' . $config->tglUploadKeuanganTerakhir;
        $pesanStok = 'upload terakhir: ' . $config->tglUploadStokTerakhir;
        $pesanOrder = 'upload terakhir: ' . $config->tglUploadOrderTerakhir;



        $this->halaman = (new crud)->make()->
            // customView("custom.marketing.shopee")->
            route("create", 'index')
            ->infoIndex(...[
                'nama' => ['icon' => 'user', 'isi' => $config->nama],
                'marketplace' => ['isi' => $config->marketplace],
            ]);


        if ($config->autosinkron) {
            $this->fields =
                [
                    (new statis)->make('nama')->default($config->nama),
                    (new statis)->make('marketplace')->default($config->marketplace),
                    (new statis)->make('konsumen')->default($config->kontak->namaLengkap),
                    (new statis)->make('kasMarketplace')->default($config->kas->nama),
                    (new statis)->make('kasPenarikan')->default($config->penarikan->nama),
                    (new statis)->make('cabang')->default($config->cabang->nama),
                    // (new file)->make('keuangan')->formInfo($pesanKeuangan),
                    // (new file)->make('konek')->judul('sinkronkan produk'),
                ];
        } else {
            $this->fields =
                [
                    (new statis)->make('nama')->default($config->nama),
                    (new statis)->make('marketplace')->default($config->marketplace),
                    (new statis)->make('konsumen')->default($config->kontak->namaLengkap),
                    (new statis)->make('kasMarketplace')->default($config->kas->nama),
                    (new statis)->make('kasPenarikan')->default($config->penarikan->nama),
                    (new statis)->make('cabang')->default($config->cabang->nama),
                    // (new file)->make('order')->formInfo($pesanOrder),
                    // (new file)->make('keuangan')->formInfo($pesanKeuangan),
                    // (new file)->make('stok')->formInfo($pesanStok),

                ];
        }
    }


    public function store()
    {




        request()->validate([
            'order' => 'required_without_all:konek,keuangan,stok|mimes:csv',
            'keuangan' => 'required_without_all:konek,order,stok',
            'stok' => 'required_without_all:konek,order,keuangan|mimes:csv',
            'konek' => 'required_without_all:stok,order,keuangan|mimes:csv',
        ]);



        $config = marketplaceConfig::find(ambil('marketplace'));



        $redirect = url('bisnis/marketplace/marketplace/' . ambil('marketplace') . '/upload/create');

        // marketplaceConfig::find(ambil('marketplace'))->update(['baruOrder'=>0]);

        if (request()->order) {

            try {
                // Excel::import(new marketplaceOrder(), request()->order);

                $this->marketplaceOrder();

                // Proses import berhasil
            } catch (\Exception $e) {
                // Tangani kesalahan validasi header di sini
                Session::flash('flash_error', $e->getMessage());
                return redirect($redirect);
            }




            Session::flash('flash_message', __('crud.simpan'));
            return redirect($redirect);
        }


        if (request()->keuangan) {



            try {


                $this->marketplaceKeuangan();

                // Proses import berhasil
            } catch (\Exception $e) {
                // Tangani kesalahan validasi header di sini
                Session::flash('flash_message', $e->getMessage());
                return redirect($redirect);
            }


            Session::flash('flash_message', __('crud.simpan'));
            return redirect($redirect);
        }


        if (request()->stok) {

            try {
                $this->marketplaceStok();
            } catch (\Exception $e) {
                // Tangani kesalahan validasi header di sini
                Session::flash('flash_error', $e->getMessage());
                return redirect($redirect);
            }
        }

        if (request()->konek) {

            try {
                $this->marketplaceKonek();
            } catch (\Exception $e) {
                // Tangani kesalahan validasi header di sini
                Session::flash('flash_error', $e->getMessage());
                return redirect($redirect);
            }



            Session::flash('flash_message', __('crud.simpan'));
            return redirect($redirect);
        }
    }
}
