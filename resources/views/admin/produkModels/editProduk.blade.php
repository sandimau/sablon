@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">Edit Produk</div>
                <div class="card-body">
                    <form action="{{ route('produkModel.updateProduk', ['produkModel' => $produkModel->id, 'produk' => $produk->id]) }}" method="POST">
                        @csrf
                        @method('patch')
                        <input type="hidden" name="produk_model_id" value="{{ $produkModel->id }}">
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama', $produk->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status">
                                <option value="1" {{ $produk->status == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $produk->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">HPP (Harga Pokok Penjualan)</label>
                            <input type="number" step="0.01" class="form-control @error('hpp') is-invalid @enderror" name="hpp" value="{{ old('hpp', $produk->hpp) }}">
                            @error('hpp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <select class="form-select @error('produk_model_id') is-invalid @enderror" name="produk_model_id">
                                @foreach ($produkModels as $item)
                                    <option value="{{ $item->id }}" {{ $produk->produk_model_id == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('produkModel.show', ['id' => $produkModel->id, 'kategori' => $produkModel->kategori_id]) }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
