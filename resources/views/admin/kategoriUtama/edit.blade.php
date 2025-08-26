@extends('layouts.app')

@section('title')
    Edit Produk Kategori Utama
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Kategori Utama</div>

                <div class="card-body">
                    <form action="{{ route('kategoriUtama.update', $kategoriUtama) }}" method="POST">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $kategoriUtama->nama) }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <input type="hidden" name="jual" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="jual" name="jual" value="1" {{ old('jual', $kategoriUtama->jual) ? 'checked' : '' }}>
                                <label class="form-check-label" for="jual">Jual</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <input type="hidden" name="beli" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="beli" name="beli" value="1" {{ old('beli', $kategoriUtama->beli) ? 'checked' : '' }}>
                                <label class="form-check-label" for="beli">Beli</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <input type="hidden" name="stok" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="stok" name="stok" value="1" {{ old('stok', $kategoriUtama->stok) ? 'checked' : '' }}>
                                <label class="form-check-label" for="stok">Stok</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <input type="hidden" name="produksi" value="0">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="produksi" name="produksi" value="1" {{ old('produksi', $kategoriUtama->produksi) ? 'checked' : '' }}>
                                <label class="form-check-label" for="produksi">Produksi</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('kategori.indexByKategoriUtama', $kategoriUtama) }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
