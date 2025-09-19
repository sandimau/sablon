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
                            @foreach ($asets as $kategori => $products)
                                @php
                                    $kategoriTotal = 0;
                                @endphp
                                <tr class="bg-light">
                                    <th colspan="5">{{ $kategori }}</th>
                                </tr>
                                @foreach ($products as $product)
                                    @php
                                        $hargaBeli = $product->harga_beli ? $product->harga_beli : $product->hpp;
                                    @endphp
                                    <tr>
                                        <td></td>
                                        <td>{{ $product->nama }}</td>
                                        <td class="text-right">{{ number_format($product->saldo, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($hargaBeli, 0, ',', '.') }}</td>
                                        <td class="text-right">Rp
                                            {{ number_format($product->saldo * $hargaBeli, 0, ',', '.') }}</td>
                                    </tr>
                                    @php
                                        $kategoriTotal += $product->saldo * $hargaBeli;
                                        $grandTotal += $product->saldo * $hargaBeli;
                                    @endphp
                                @endforeach
                                <tr class="table-info">
                                    <td colspan="4" class="text-right"><strong>Total {{ $kategori }}</strong></td>
                                    <td class="text-right"><strong>Rp
                                            {{ number_format($kategoriTotal, 0, ',', '.') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
