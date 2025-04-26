@extends('layouts.app')

@section('title')
    Target Detail Operator
@endsection

@section('content')
    <div class="bg-light rounded">
        @include('layouts.includes.messages')
        <div class="row">
            <div class="col-lg-12">
                <header class="header mb-4">
                    <div class="container-fluid">
                        <div>
                            <h5 class="card-title">Rekap Bulan {{ date('F Y') }}</h5>
                            <h5 class=" text-red-800">Operator: {{ $operator }}</h5>
                        </div>
                        <h5>Total Resi: {{ $totalOperator }}</h5>
                        <h5>Total Point: {{ $totalJumlah }}</h5>
                    </div>
                </header>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            @foreach ($groupedOperators as $date => $operatorGroup)
                                <table class="table table-striped table-hover mb-4">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Resi</th>
                                            <th>Point</th>
                                            <th>Konsumen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($operatorGroup as $operator)
                                            <tr>
                                                <td>{{ $operator->created_at->format('d F Y') }}</td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $operator->jumlah }}</td>
                                                <td>{{ $operator->konsumen }}</td>
                                            </tr>
                                            @if ($loop->last)
                                                <tr>
                                                    <td><strong>total: <br>{{ $operatorGroup->count() }}</strong></td>
                                                    <td><strong>total: <br>{{ $operatorGroup->sum('jumlah') }}</strong>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $operators->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
