@extends('layouts.app')

@section('title')
    Data Belum Lunas
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Belum Lunas</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    {{ $orders->links() }}
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>kontak</th>
                                <th>nota</th>
                                <th>total tagihan</th>
                                <th>dp</th>
                                <th>kekurangan</th>
                                <th>action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $item)
                                <tr>
                                    <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                    <td><a href="{{ route('kontaks.show', $item->kontak_id) }}">{{ $item->kontak->nama }}</a></td>
                                    <td>#{{ $item->id }}</td>
                                    <td>{{ number_format($item->total, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->bayar, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->kekurangan, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('order.bayar',$item->id) }}"
                                            class="btn btn-info btn-sm me-1 text-white"><i class='bx bx-dollar-circle'></i>
                                            bayar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        let table = new DataTable('#myTable');
    </script>
@endpush
