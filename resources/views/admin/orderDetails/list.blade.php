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
    </div>
@endsection
