@extends('layouts.app')

@section('title', 'Review Lembur Freelancer')

@section('content')
<div class="card">
    <div class="card-header">Review Lembur</div>
    <div class="card-body">
        <form action="{{ route('freelance_overtime.update', $freelanceOvertime->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan</label>
                        <input type="text" class="form-control" value="{{ $freelanceOvertime->created_at->format('d M Y') }}" disabled>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" value="{{ $freelanceOvertime->freelance->nama }}" disabled>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Jam Lembur</label>
                        <input type="text" class="form-control" value="{{ $freelanceOvertime->jam_lembur }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="rate_lembur_per_jam" class="form-label">Upah Perjam</label>
                        <input type="text" name="rate_lembur_per_jam" class="form-control" value="{{$freelance->rate_lembur_per_jam ?? ''}}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Jumlah Upah</label>
                        <input type="text" name="jumlah_upah" class="form-control" value="Rp {{ number_format($freelanceOvertime->jumlah_upah, 0, ',', '.') }}" disabled>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="akun_detail_id" class="form-label">Kas</label>
                        <select name="akun_detail_id" id="akun_detail_id" class="form-select select2" required>
                            <option value="">--Pilih--</option>
                            @foreach($akunDetails as $akunDetail)
                                <option value="{{ $akunDetail->id }}" {{ $freelanceOvertime->akun_detail_id == $akunDetail->id ? 'selected' : '' }}>
                                    {{ $akunDetail->nama }} ({{ $akunDetail->akun_kategori->nama }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select select2">
                            <option value="">--Pilih--</option>
                            <option value="456" {{ $freelanceOvertime->kategori==456 ? 'selected' : '' }}>Lembur Printing</option>
                            <option value="464" {{ $freelanceOvertime->kategori==464 ? 'selected' : '' }}>Lembur Sublime</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending" {{ $freelanceOvertime->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $freelanceOvertime->status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $freelanceOvertime->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan_akunting" class="form-label">Catatan Akunting</label>
                <textarea name="catatan_akunting" id="catatan_akunting" class="form-control">{{ $freelanceOvertime->catatan_akunting }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('freelance_overtime.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
