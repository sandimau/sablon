@extends('layouts.app')

@section('title')
Edit Akuns
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        akuns
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("kategori.update", $kategori->id) }}" enctype="multipart/form-data">
            @method('patch')
            @csrf
            <div class="form-group mb-3">
                <label for="nama">nama</label>
                <input class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}" type="text" name="nama" id="nama" value="{{ old('nama', $kategori->nama) }}">
                @if($errors->has('nama'))
                    <div class="invalid-feedback">
                        {{ $errors->first('nama') }}
                    </div>
                @endif
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input name="jual" class="form-check-input {{ $errors->has('jual') ? 'is-invalid' : '' }}"
                        type="checkbox" value="1" id="flexCheckDefault" {{ $kategori->jual ? 'checked' : null }}>
                    <label class="form-check-label" for="flexCheckDefault">
                        Jual
                    </label>
                    @if ($errors->has('jual'))
                        <div class="invalid-feedback">
                            {{ $errors->first('jual') }}
                        </div>
                    @endif
                </div>
                <div class="form-check">
                    <input name="beli" class="form-check-input {{ $errors->has('beli') ? 'is-invalid' : '' }}"
                        type="checkbox" value="1" id="flexCheckDefault" {{ $kategori->beli ? 'checked' : null }}>
                    <label class="form-check-label" for="flexCheckDefault">
                        Beli
                    </label>
                    @if ($errors->has('beli'))
                        <div class="invalid-feedback">
                            {{ $errors->first('beli') }}
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="kategori_utama_id">Kategori Utama</label>
                    <select name="kategori_utama_id" id="kategori_utama_id" class="form-control">
                        <option value="">Pilih Kategori Utama</option>
                        @foreach ($kategoriUtamas as $kategoriUtama)
                            <option value="{{ $kategoriUtama->id }}" {{ $kategori->kategori_utama_id == $kategoriUtama->id ? 'selected' : '' }}>{{ $kategoriUtama->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
