@extends('layouts.app')

@section('title')
    Target
@endsection

@section('content')
    <div class="bg-light rounded">
        @include('layouts.includes.messages')
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-details-center">
                            <div>
                                <h5 class="card-title">Target {{ date('d F Y') }}</h5>
                            </div>
                            <div>
                                <a href="{{ route('orderDetail.semuaList') }}" class="btn btn-primary">Semua Data</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($operators as $operator)
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $operator->nama }}</h5>
                                            <p>
                                                jumlah Resi: {{ $operator->total }}
                                                <br>
                                                Point: {{ $operator->total_jumlah }}
                                            </p>
                                            <a href="{{ route('orderDetail.listOperatorDetail', $operator->nama) }}" class="btn btn-success">Detail</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
                @if($operatorsBulanSebelumnya->count() > 0)
        @php
            $groupedByMonth = $operatorsBulanSebelumnya->groupBy(function($item) {
                return $item->tahun . '-' . $item->bulan;
            });
        @endphp

        @foreach($groupedByMonth as $monthKey => $operatorsInMonth)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Target {{ $operatorsInMonth->first()->nama_bulan }} {{ $operatorsInMonth->first()->tahun }}</h5>
                        <div class="row">
                            @foreach($operatorsInMonth as $operator)
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $operator->nama }}</h6>
                                        <p class="mb-1">Total Resi: <strong>{{ $operator->total }}</strong></p>
                                        <p class="mb-1">Total Point: <strong>{{ $operator->total_jumlah }}</strong></p>
                                        <a href="{{ route('orderDetail.listOperatorDetailBulan', ['operator' => $operator->nama, 'bulan' => $operator->bulan, 'tahun' => $operator->tahun]) }}" class="btn btn-success btn-sm">Detail</a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
@endsection
