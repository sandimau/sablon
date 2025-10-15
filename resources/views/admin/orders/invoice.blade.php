@extends('layouts.app')

@section('title')
    Update Orders
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <button id="print" class="btn btn-primary" type="button"><i class='bx bx-printer'></i> Print</button>
        </div>
        <div class="card-body printableArea">
            <div class="row">
                <div class="col-sm-12 col-lg-6">
                    <img style="height: 80px" src="{{ url('uploads/logo/' . $sistems['logo']) }}" alt="" srcset="">
                </div>
                <div class="col-sm-12 col-lg-6">
                    <address class="mt-4">
                        <h3 class="test"><b>Kantor </b></h3>
                        <p class="text-blue test">
                            {{ $sistems['alamat'] }}
                        </p>
                    </address>
                </div>

                <div class="col-md-12">
                    <hr class="hr-invoice">
                    <div class="d-flex justify-content-between">
                        <div>
                            <address>
                                <h5>kepada: <br> {{ $order->kontak->nama }}</h5>
                            </address>
                        </div>
                        <div>
                            <p>
                                <b>invoice #{{ $order->id }} </b>
                                <br> <i class='bx bx-calendar'></i> {{ date('d-m-Y', strtotime($order->created_at)) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>produk</th>
                                    <th>tema</th>
                                    <th class="test">Jumlah</th>
                                    <th class="test">Harga</th>
                                    <th class="test">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($order->orderDetail as $item)
                                    @if ($item->produksi->nama != 'batal')
                                        <tr>
                                            <td class="text-center">{{ $i++ }}</td>
                                            <td>{{ $item->produk->nama }}</td>
                                            <td>{{ $item->tema }}</td>
                                            <td class="test">{!! number_format($item->jumlah) !!}</td>
                                            <td class="test">{!! number_format($item->harga) !!}</td>
                                            <td class="test">{!! number_format($item->harga * $item->jumlah) !!}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <p class="text-center" style="font-size: 13px">Hormat Kami
                                <br>
                                <br>
                                @if ($ars && $ars->ttd)
                                    <div style="position: relative; display: flex; justify-content: center; align-items: center;">
                                        <img id="signature-image" src="{{ url('uploads/ttd/' . $ars->ttd) }}" alt="Tanda Tangan" style="height: 60px; visibility: hidden; position: relative; z-index: 1;">
                                        <img id="stempel-image" src="{{ url('img/stempel.png') }}" alt="Stempel" style="height: 40px; visibility: hidden; transform: rotate(22deg); position: absolute; top: 0px; left: 130px; z-index: 2;">
                                    </div>
                                @endif
                                @if ($member)
                                    <span id="member-name" style="cursor: pointer; display: block; text-align: center;">{{ $member->nama_lengkap }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-lg-5 col-sm-6">
                            <p style="font-size: 13px" class="text-center">pembayaran bisa dilakukan
                                melalui transfer ke rekening berikut: <br>
                                {{ $sistems['rek'] }}
                            </p>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="d-flex justify-content-between">
                                <div class="test">
                                    <div style="font-weight: 600">Total :</div>
                                    <div>sudah dibayar :</div>
                                    <div style="font-weight: 600">kekurangan :</div>
                                </div>
                                <div class="test">
                                    <div>{{ number_format($order->total, 0, ',', '.') }}</div>
                                    <div>{{ number_format($order->pembayaran, 0, ',', '.') }}</div>
                                    <div style="font-weight: 600">{{ number_format($order->kekurangan, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function() {
            $("#print").click(function() {
                var mode = 'iframe'; //popup
                var close = mode == "popup";
                var options = {
                    mode: mode,
                    popClose: close
                };
                $("div.printableArea").printArea(options);
            });
        });

        function myFunction() {
            var x = document.getElementById("ttd");
            if (x.style.visibility === "hidden") {
                x.style.visibility = "visible";
            } else {
                x.style.visibility = "hidden";
            }
            var x = document.getElementById("stempel");
            if (x.style.visibility === "hidden") {
                x.style.visibility = "visible";
            } else {
                x.style.visibility = "hidden";
            }
        }

        // Counter untuk tracking jumlah klik
        var clickCount = 0;

        // Function untuk mengatur tampilan berdasarkan jumlah klik
        function handleClick() {
            var signatureImg = document.getElementById("signature-image");
            var stempelImg = document.getElementById("stempel-image");

            clickCount++;

            if (clickCount === 1) {
                // Klik 1: Tampilkan tanda tangan saja
                if (signatureImg) {
                    signatureImg.style.visibility = "visible";
                }
                if (stempelImg) {
                    stempelImg.style.visibility = "hidden";
                }
            } else if (clickCount === 2) {
                // Klik 2: Tampilkan tanda tangan dan stempel
                if (signatureImg) {
                    signatureImg.style.visibility = "visible";
                }
                if (stempelImg) {
                    stempelImg.style.visibility = "visible";
                }
            } else if (clickCount === 3) {
                // Klik 3: Sembunyikan semua
                if (signatureImg) {
                    signatureImg.style.visibility = "hidden";
                }
                if (stempelImg) {
                    stempelImg.style.visibility = "hidden";
                }
                clickCount = 0; // Reset counter
            }
        }

        // Add click event listener to member name
        $(document).ready(function() {
            $("#member-name").click(function() {
                handleClick();
            });
        });
    </script>
    <style>
        @media only screen and (min-width: 600px) {
            .test {
                text-align: right !important;
            }
        }
    </style>
@endpush
