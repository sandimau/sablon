@extends('layouts.app')

@section('title')
    Target Detail Operator
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
                                <h5 class="card-title">Target detail {{ $operator }}</h5>
                                <p>Total Resi: {{ $totalOperator }}</p>
                                <p>Total Point: {{ $totalJumlah }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @foreach ($groupedOperators as $date => $operatorGroup)
                                <h5 class="mb-3">{{ $date }}</h5>
                                <table class="table table-striped table-hover mb-4">
                                    <thead>
                                        <tr>
                                            <th>Jumlah</th>
                                            <th>Konsumen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($operatorGroup as $operator)
                                            <tr>
                                                <td>{{ $operator->jumlah }}</td>
                                                <td>{{ $operator->konsumen }}</td>
                                            </tr>
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
