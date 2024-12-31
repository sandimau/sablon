@extends('layouts.app')

@section('title')
    Bayar Orders
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add bayar</h5>
                </div>
                <a href="{{ route('order.dashboard') }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('order.storeBayar') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="form-group mb-3">
                    <label for="tema">konsumen</label>
                    <input class="form-control" type="text" value="{{ $order->kontak->nama }}" disabled>
                </div>
                <div class="form-group mb-3">
                    <label for="tema">total tagihan</label>
                    <input id="total" class="form-control" type="text" value="{{ $order->total }}" disabled>
                </div>
                <div class="form-group mb-3">
                    <label for="tema">pembayaran</label>
                    <input id="pembayaran" class="form-control" type="text" value="{{ $order->bayar }}" disabled>
                </div>
                <div class="form-group mb-3">
                    <label for="tema">kekurangan</label>
                    <input id="kekurangan" class="form-control" type="text" value="{{ $order->kekurangan }}" disabled>
                </div>
                <div class="form-group mb-3">
                    <label for="diskon">diskon</label>
                    <input id="diskon" onchange="updateSubTotal()" class="form-control {{ $errors->has('diskon') ? 'is-invalid' : '' }}" type="number"
                        name="diskon" id="diskon">
                    @if ($errors->has('diskon'))
                        <div class="invalid-feedback">
                            {{ $errors->first('diskon') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="jumlah">Jumlah</label>
                    <input id="jumlah" class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number"
                        name="jumlah" id="jumlah" value="{{ $order->kekurangan }}">
                    @if ($errors->has('jumlah'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jumlah') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="akun_detail_id">kas</label>
                    <select class="form-select {{ $errors->has('akun_detail_id') ? 'is-invalid' : '' }}"
                        aria-label="Default select example" name="akun_detail_id" id="akun_detail_id">
                        <option >-- pilih kas --</option>
                        @foreach ($kas as $id => $entry)
                            <option value="{{ $id }}">{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('akun_detail_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('akun_detail_id') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="tanggal">tanggal</label>
                    <input class="form-control {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" type="date"
                        name="tanggal" id="tanggal" value="{{ old('tanggal', '') }}">
                    @if ($errors->has('tanggal'))
                        <div class="invalid-feedback">
                            {{ $errors->first('tanggal') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="ket">ket</label>
                    <input id="ket" class="form-control" type="text"
                        name="ket" id="ket">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        function updateSubTotal() {
            let total = document.getElementById('total');
            let diskon = document.getElementById('diskon');
            let jumlah = document.getElementById('jumlah');
            let kekurangan = document.getElementById('kekurangan');
            let pembayaran = document.getElementById('pembayaran');
            total.value = <?php echo $order->total + $order->diskon ?> - diskon.value;
            kekurangan.value = jumlah.value = total.value  - pembayaran.value;
        }
    </script>
@endpush
