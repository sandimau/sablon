@extends('layouts.app')

@section('title')
    Bayar Kasbon Freelance
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Bayar Kasbon</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $freelance->nama }}</h6>
                </div>
                <a href="{{ route('freelances.show', ['freelance' => $freelance->id, 'tab' => 'kasbon']) }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Info Saldo -->
            <div class="alert alert-info mb-4">
                <strong>Saldo Kasbon Saat Ini:</strong> Rp {{ number_format($kasbon->saldo ?? 0, 0, ',', '.') }}
            </div>

            <form method="POST" action="{{ route('freelance_kasbon.bayarStore') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="freelance_id" value="{{ $freelance->id }}">
                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal</label>
                    <input class="form-control date {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" type="date"
                        name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}">
                    @if ($errors->has('tanggal'))
                        <div class="invalid-feedback">
                            {{ $errors->first('tanggal') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label class="required" for="jumlah">Jumlah Pembayaran</label>
                    <input class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $kasbon->saldo ?? 0) }}" required max="{{ $kasbon->saldo ?? 0 }}">
                    @if($errors->has('jumlah'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jumlah') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id="keterangan"
                        cols="30" rows="5">{{ old('keterangan', 'Pembayaran kasbon') }}</textarea>
                    @if ($errors->has('keterangan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('keterangan') }}
                        </div>
                    @endif
                </div>
                <div class="form-group mb-3">
                    <label for="akun_detail_id">Kas</label>
                    <select class="form-select {{ $errors->has('akun_detail_id') ? 'is-invalid' : '' }}" aria-label="Default select example" name="akun_detail_id" id="akun_detail_id">
                        @foreach($kas as $id => $entry)
                            <option value="{{ $id }}" {{ old('akun_detail_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('akun_detail_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('akun_detail_id') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        <i class="bx bx-money"></i> Bayar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

