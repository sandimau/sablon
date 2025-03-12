<?php

namespace App\Jobs;

use App\Models\Produksi;
use Illuminate\Support\Facades\DB;

class DeleteOrders
{
    public function deleteOrders()
    {
        $batal_id = Produksi::ambilFlow('batal');
        // Get marketplace contact IDs
        $marketplaceContactIds = DB::table('kontaks')
            ->where('marketplace', 1)
            ->pluck('id');

        // Delete orders and their details in a single query per table
        DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.produksi_id', $batal_id)
            ->whereIn('orders.kontak_id', $marketplaceContactIds)
            ->delete();

        DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->where('order_details.produksi_id', $batal_id)
            ->whereIn('orders.kontak_id', $marketplaceContactIds)
            ->delete();
    }
}
