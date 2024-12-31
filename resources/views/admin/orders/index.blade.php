@extends('layouts.app')

@section('title')
    Data Orders
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">orders</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your orders here.</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                {{ $orders->links() }}
                <div class="table-responsive">
                    <table class="table table-striped table-hover" >
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Konsumen</th>
                                <th>Order</th>
                                <th>Total</th>
                                <th>Kekurangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr data-entry-id="{{ $order->id }}">
                                    <td>{{ date('d-m-Y', strtotime($order->created_at)) }}</td>
                                    <td>{{ $order->kontak->nama ?? '' }}</td>
                                    <td><a href="{{ route('order.detail', $order->id) }}">{{ $order->listproduk }}</a></td>
                                    <td>{{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td>{{ number_format($order->kekurangan, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($order->bayar == 0 && $order->total > 0)
                                            <a href="{{ route('order.unpaid') }}"
                                                class="btn rounded-pill btn-danger btn-sm text-white">belum bayar</a>
                                        @endif
                                        @if ($order->bayar == 0 && $order->total == 0)
                                            <a href="{{ route('order.unpaid') }}"
                                                class="btn rounded-pill btn-danger btn-sm text-white">batal</a>
                                        @endif
                                        @if ($order->total > $order->bayar && $order->bayar > 0)
                                            <a href="{{ route('order.unpaid') }}"
                                                class="btn rounded-pill btn-warning btn-sm text-white">belum lunas</a>
                                        @endif
                                        @if ($order->total == $order->bayar && $order->bayar != 0  && $order->total != 0)
                                            <button class="btn rounded-pill btn-success btn-sm text-white">lunas</button>
                                        @endif
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
