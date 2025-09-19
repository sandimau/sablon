@extends('layouts.app')

@section('title')
    Add New Freelancer
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            Create New Freelancer
        </div>
        <div class="card-body">
            <form action="{{ route('freelances.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="user_id">User Login</label>
                            <select name="user_id" id="user_id" class="form-control">
                                @foreach ($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="alamat">Alamat</label>
                    <textarea name="alamat" id="alamat" cols="30" rows="10" class="form-control"></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" name="tanggal_masuk" id="tanggal_masuk">
                    </div>
                    <div class="col-md-4">
                        <label for="upah" class="form-label">Upah</label>
                        <input type="text" class="form-control" name="upah" id="upah">
                    </div>
                    <div class="col-md-4">
                        <label for="handphone" class="form-label">Handphone</label>
                        <input type="text" class="form-control" name="handphone" id="handphone">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="rate_lembur_per_jam">Lembur Per Jam</label>
                            <input type="text" class="form-control" name="rate_lembur_per_jam" id="rate_lembur_per_jam">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="nomor_rekening">Nomor Rekening</label>
                    <input type="text" class="form-control" name="nomor_rekening" id="nomor_rekening">
                </div>
                <div class="form-group mb-3">
                    <label for="bank">Bank</label>
                    <input type="text" class="form-control" name="bank" id="bank">
                </div>
                <div class="form-group mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $model->is_active ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-primary" type="submit">
                        {{ trans('save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection