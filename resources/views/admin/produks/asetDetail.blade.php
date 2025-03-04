@extends('layouts.app')

@section('title')
    Data Asets
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Aset per Produk</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Nama Produk</th>
                            <th class="text-right">Saldo</th>
                            <th class="text-right">Harga Beli</th>
                            <th class="text-right">Total Aset</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = 0;
                        @endphp
                        @foreach($asets as $kategori => $products)
                            @php
                                $kategoriTotal = 0;
                            @endphp
                            <tr class="bg-light">
                                <th colspan="5">{{ $kategori }}</th>
                            </tr>
                            @foreach($products as $product)
                                <tr>
                                    <td></td>
                                    <td>{{ $product->nama }}</td>
                                    <td class="text-right">{{ number_format($product->saldo, 2) }}</td>
                                    <td class="text-right">Rp {{ number_format($product->harga_beli, 2) }}</td>
                                    <td class="text-right">Rp {{ number_format($product->total_aset, 2) }}</td>
                                </tr>
                                @php
                                    $kategoriTotal += $product->total_aset;
                                    $grandTotal += $product->total_aset;
                                @endphp
                            @endforeach
                            <tr class="table-info">
                                <td colspan="4" class="text-right"><strong>Total {{ $kategori }}</strong></td>
                                <td class="text-right"><strong>Rp {{ number_format($kategoriTotal, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
