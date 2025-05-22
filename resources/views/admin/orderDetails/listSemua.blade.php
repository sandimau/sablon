@extends('layouts.app')

@section('title')
    Semua Operator
@endsection

@section('content')
    <div class="bg-light rounded">
        @include('layouts.includes.messages')
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-details-center">
                            <h5 class="card-title">semua</h5>
                            <form action="" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Cari nama..." value="{{ request('search') }}">
                                <input type="text" name="konsumen" class="form-control me-2" placeholder="Cari konsumen..." value="{{ request('konsumen') }}">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-4">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Point</th>
                                        <th>Konsumen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operators as $operator)
                                        <tr>
                                            <td>{{ $operator->created_at->format('d F Y') }}</td>
                                            <td>{{ $operator->nama }}</td>
                                            <td>{{ $operator->jumlah }}</td>
                                            <td>{{ $operator->konsumen }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $operators->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
