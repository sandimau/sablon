@extends('layouts.app')

@section('title')
    Data Asets
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Asets</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">Kategori</th>
                                <th scope="col" style="text-align: right;">Total Aset</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalAllAsets = 0; @endphp
                            @foreach ($asets as $kategori => $items)
                                @php
                                    $totalAset = $items->sum('total_aset');
                                    $totalAllAsets += $totalAset;
                                @endphp
                                <tr>
                                    <td><a href="{{ route('produks.asetDetail', ['kategori' => $items->first()->kategori_id]) }}">{{ $kategori }}</a></td>
                                    <td style="text-align: right;">
                                        {{ number_format($totalAset, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td style="text-align: right;">
                                    {{ number_format($totalAllAsets, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
