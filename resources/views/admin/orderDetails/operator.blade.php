@extends('layouts.app')

@section('title')
    Create Orders
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add order</h5>
                </div>
                <a href="{{ route('order.dashboard') }}" class="btn btn-success ">back</a>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('orderDetail.operatorStore') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_detail_id" value="{{ $detail->id }}">
                <input type="hidden" name="konsumen" value="{{ $detail->order->username }}">
                <input type="hidden" name="jumlah" value="{{ $detail->jumlah }}">
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama') }}">
                </div>
                <div class="form-group mt-3">
                    <label for="konsumen">Konsumen</label>
                    <input type="text" class="form-control" id="konsumen" value="{{ $detail->order->username }}" disabled>
                </div>

                <div class="form-group mt-3">
                    <label for="jumlah">Jumlah</label>
                    <input type="text" class="form-control" id="jumlah" value="{{ $detail->jumlah }}" disabled>
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
