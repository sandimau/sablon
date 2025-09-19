@extends('layouts.app')

@section('title')
    Edit Freelancer
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            Edit Freelancer
        </div>
        <div class="card-body">
            <form action="{{ route('freelances.update', $freelance->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $freelance->nama) }}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="user_id">User Login</label>
                            <select name="user_id" id="user_id" class="form-control">
                                @foreach ($users as $user)
                                    <option value="{{$user->id}}" {{ old('user_id', $freelance->user_id) == $user->id ? 'selected' : '' }}>{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="alamat">Alamat</label>
                    <textarea name="alamat" id="alamat" cols="30" rows="10" class="form-control">{{ old('alamat', $freelance->alamat) }}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" name="tanggal_masuk" id="tanggal_masuk"
                               value="{{ old('tanggal_masuk', \Carbon\Carbon::parse($freelance->tanggal_masuk)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="upah" class="form-label">Upah</label>
                        <input type="text" class="form-control" name="upah" id="upah" value="{{ old('upah', $freelance->upah) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="handphone" class="form-label">Handphone</label>
                        <input type="text" class="form-control" name="handphone" id="handphone" value="{{ old('handphone', $freelance->handphone) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="rate_lembur_per_jam">Lembur Per Jam</label>
                            <input type="text" class="form-control" name="rate_lembur_per_jam" id="rate_lembur_per_jam" value="{{ old('rate_lembur_per_jam', $freelance->rate_lembur_per_jam) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="nomor_rekening">Nomor Rekening</label>
                    <input type="text" class="form-control" name="nomor_rekening" id="nomor_rekening" value="{{ old('nomor_rekening', $freelance->nomor_rekening) }}">
                </div>

                <div class="form-group mb-3">
                    <label for="bank">Bank</label>
                    <input type="text" class="form-control" name="bank" id="bank" value="{{ old('bank', $freelance->bank) }}">
                </div>

                <div class="form-group mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $freelance->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <button class="btn btn-primary" type="submit">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
