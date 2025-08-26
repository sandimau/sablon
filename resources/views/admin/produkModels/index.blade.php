@extends('layouts.app')

@section('title')
    Produk Model
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><a href="{{ route('kategoriUtama.index') }}"
                    class="text-decoration-none text-primary">{{ $kategori->kategoriUtama->nama }}</a> > <a
                    href="{{ route('kategori.indexByKategoriUtama', $kategori->kategoriUtama->id) }}"
                    class="text-decoration-none text-primary">{{ $kategori->nama }}</a> > Produk</h5>
            <a href="{{ route('produkModel.create', ['kategori' => $kategori->id]) }}" class="btn btn-primary">Tambah
                Produk</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>varian</th>
                            <th>Satuan</th>
                            <th>harga beli</th>
                            <th>harga jual</th>
                            <th>hpp</th>
                            <th>stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $modelId = null;
                            $prevModel = null;
                        @endphp
                        @foreach ($produks as $produk)
                            @php
                                $modelId = $produk->model_id;
                                $showModel = $prevModel !== $produk->model;
                                $prevModel = $produk->model;
                            @endphp
                            <tr>
                                <td>{{ $produk->produk_id }}</td>
                                <td>
                                    @if ($produk->gambar)
                                        @if ($showModel)
                                            <a class="test-popup-link" href="{{ asset('uploads/produk/' . $produk->gambar) }}">
                                                <img style="height: 60px" src="{{ url('uploads/produk/' . $produk->gambar) }}"
                                                    alt="" srcset="">
                                            </a>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($showModel)
                                        <a
                                            href="{{ route('produkModel.show', ['id' => $produk->model_id, 'kategori' => $kategori->id]) }}">{{ $produk->model }}</a>
                                    @endif
                                </td>
                                <td>{{ $produk->varian }}</td>
                                <td>{{ $produk->satuan }}</td>
                                <td>Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($produk->hpp, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('produkStok.index', ['produk' => $produk->produk_id]) }}">{{ $produk->saldo ?? 0 }}</a>
                                </td>
                                <td>
                                    <form action="{{ route('produkModel.destroy', ['produkModel' => $produk->produk_id, 'kategori' => $kategori->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
