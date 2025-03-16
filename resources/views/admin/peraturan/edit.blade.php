@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Peraturan Pegawai</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('peraturan.update', $peraturan) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group mb-3">
                    <label>Judul</label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $peraturan->judul) }}">
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label>Isi Peraturan</label>
                    <textarea id="isi" name="isi" class="form-control @error('isi') is-invalid @enderror" rows="5">{{ old('isi', $peraturan->isi) }}</textarea>
                    @error('isi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label>Tanggal Berlaku</label>
                    <input type="date" name="tanggal_berlaku" class="form-control @error('tanggal_berlaku') is-invalid @enderror" value="{{ old('tanggal_berlaku', $peraturan->tanggal_berlaku->format('Y-m-d')) }}">
                    @error('tanggal_berlaku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="aktif" {{ old('status', $peraturan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $peraturan->status) == 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('peraturan.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="js/vendor/jquery-3.3.1.min.js"><\/script>')
    </script>

    <script src="{{ asset('dist/trumbowyg.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('dist/ui/trumbowyg.min.css') }}">

    <script>
        $(document).ready(function() {
            $('#isi').trumbowyg();
        });
    </script>
@endpush