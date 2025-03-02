@extends('layouts.app')

@section('title')
    Data Aset dan Omzet Produk
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Aset dan Omzet Produk</h4>
            <form method="get" class="d-flex align-items-center">
                <label for="year" class="me-2">Pilih Tahun:</label>
                <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">Kategori</th>
                            <th rowspan="2" class="text-end">Aset</th>
                            <th colspan="12" class="text-center">Omzet {{ $selectedYear }}</th>
                        </tr>
                        <tr>
                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $bulan)
                                <th class="text-end">{{ $bulan }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAset = 0;
                            $totalOmzetPerBulan = array_fill(1, 12, 0);
                            $currentKategori = '';
                        @endphp

                        @foreach($omzet->groupBy('kategori_id') as $kategoriId => $kategoriData)
                            @php
                                $firstData = $kategoriData->first();
                                $asetValue = $asets->where('kategori_id', $kategoriId)->first()->nilai_aset ?? 0;
                                $totalAset += $asetValue;
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('produk.omzetDetail', $kategoriId) }}">
                                        {{ $firstData->namaKategori }}
                                    </a>
                                </td>
                                <td class="text-end">{{ number_format($asetValue, 2, ',', '.') }}</td>
                                @for($bulan = 1; $bulan <= 12; $bulan++)
                                    @php
                                        $omzetBulan = $kategoriData->where('bulan', $bulan)->first()->omzet ?? 0;
                                        $totalOmzetPerBulan[$bulan] += $omzetBulan;
                                    @endphp
                                    <td class="text-end">{{ number_format($omzetBulan, 2, ',', '.') }}</td>
                                @endfor
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end">{{ number_format($totalAset, 2, ',', '.') }}</td>
                            @foreach($totalOmzetPerBulan as $totalBulan)
                                <td class="text-end">{{ number_format($totalBulan, 2, ',', '.') }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .form-select {
        width: auto;
    }
</style>
@endpush
