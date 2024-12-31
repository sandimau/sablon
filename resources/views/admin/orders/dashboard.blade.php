@extends('layouts.app')

@section('title')
    Proses Produksi
@endsection

@section('content')
    <header class="header mb-4">
        <div class="container-fluid">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb my-0 ms-2">
                    <li class="breadcrumb-item">
                        <b>Dashboard</b>
                    </li>
                </ol>
            </nav>
            @can('order_create')
                <a href="{{ route('order.create') }}" class="btn btn-primary rounded-pill text-white">Tambah Orders</a>
            @endcan
        </div>
    </header>
    <div class="bg-light rounded">
        @include('layouts.includes.messages')
        <div class="row">
            <div class="col-md-6">
                @php
                    $i = 0;
                @endphp
                @foreach ($produksi as $item)
                    @if ($item->nama != 'finish' && $item->nama != 'batal')
                        @php
                            $i++;
                            if ($i == 5) {
                                echo '</div><div class=col-md-6>';
                            }
                        @endphp
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">{{ $item->nama }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($item->orderDetail)
                                    @php
                                        $hasil = [];
                                        $tampilan = '';
                                        $order_id = 0;

                                        foreach ($item->orderDetail()->get() as $detail) {
                                            /////// tambah baris baru

                                            if ($order_id != $detail->order_id) {
                                                if ($order_id != 0) {
                                                    $tampilan .= '<div class=pull-right></div></a>';
                                                }

                                                $warna = '';
                                                $nominal = '';
                                                $order = $detail->order;

                                                $total = $order->total;
                                                if ($total < 1000000) {
                                                    $warna = 'black';
                                                    if ($total == 0) {
                                                        $nominal = 0;
                                                    } else {
                                                        $nominal = floor($total / 1000) . 'rb';
                                                    }
                                                } else {
                                                    if ($total <= 5000000) {
                                                        $warna = 'green';
                                                    } elseif ($total <= 10000000) {
                                                        $warna = '#FAA814';
                                                    } else {
                                                        $warna = '#D93007';
                                                    }

                                                    $nominal = round($total, -5) / 1000000 . 'jt';
                                                }

                                                $konsumen = $order->kontak;

                                                $model_ar = $konsumen->ar->kode ?? 'kosong';
                                                $tampilan .= "<a class='popup d-flex'  href='" . url('admin/order/' . $detail->order_id . '/detail') . "' ><p style='font-weight:600' class='text-default'>";

                                                $tampilan .= " <span class='label label-rounded' style='background-color: " . $konsumen->ar->warna . "'> " . $konsumen->ar->kode . '  </span>';

                                                $tampilan .= " <span class='label label-rounded mr-1' style='background-color: " . $warna . "'> " . $nominal . '  </span> ';

                                                $tampilan .= $konsumen->nama . ' <span style="color:#222222">' . $order->username . '</span></p>';
                                            }

                                            ////////////////ngisi order detail

                                            $proses = '';
                                            if (!empty($detail->process)) {
                                                $proses = "<span class='label label-info  label-rounded' style='background-color: " . '#' . $detail->process->warna . ";'>" . $detail->process->nama . '</span>';
                                            }

                                            $nama_produk = $detail->produk->nama;

                                            $jadwalx = '';
                                            if ($detail->deathline) {
                                                $time1 = new DateTime(date('Y-m-d'));
                                                $time2 = new DateTime($detail->deathline);
                                                $interval = $time1->diff($time2)->format('%r%a');

                                                $hasil = $interval;
                                                if ($interval == 0) {
                                                    $hasil = ' hari ini';
                                                    $class = 'warning';
                                                }
                                                if ($interval == 1) {
                                                    $hasil = ' besok';
                                                    $class = 'info';
                                                }
                                                if ($interval > 1) {
                                                    $hasil = $interval.' hari lagi';
                                                    $class = 'success';
                                                }
                                                if ($interval < 0) {
                                                    $hasil = $interval.' hari';
                                                    $class = 'danger';
                                                }

                                                $jadwalx = " <small> <span class='badge text-white text-bg-" . $class . "''>" . $hasil . '</span></small>';
                                            } else {
                                                $jadwalx = '';
                                            }

                                            $tampilan .= "<span style='color:#636363; padding-right:5px;'> " . $nama_produk . ' ' . $proses . $jadwalx . '</span> ';

                                            $order_id = $detail->order_id;
                                        }

                                        if ($order_id != 0) {
                                            $tampilan .= '<div class=pull-right></div></a>';
                                        }

                                        echo $tampilan;
                                    @endphp
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        let table = new DataTable('#myTable');
    </script>
    <style>
        a {
            text-decoration: none
        }

        .text-default {
            font-weight: 700 !important;
            margin: 0;
            padding: 10px 5px;
            color: #398bf7 !important;
        }

        .label {
            font-weight: 400;
            font-size: 13px;
            color: #ffffff;
            padding: 2px 5px;
            border-radius: 5px;
            margin-right: 8px;
        }

        .popup {
            align-items: center;
            border-bottom: 1px solid #e9e9e9;
        }

        .popup:hover {
            background-color: #e0e0e0;
            border-radius: 6px;
        }
    </style>
@endpush
