@extends('layouts.app')

@section('title')
    Kategori Utama List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Kategori Utama</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your Kategori Utama here.</h6>
                    </div>
                    @can('akun_create')
                        <a href="{{ route('kategoriUtama.create') }}" class="btn btn-primary ">Add Kategori Utama</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategoriUtamas as $index => $kategori)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a
                                            href="{{ route('kategori.indexByKategoriUtama', $kategori->id) }}">{{ $kategori->nama }}</a>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0">
                                            @if ($kategori->jual)
                                                <li>✓ Jual</li>
                                            @endif
                                            @if ($kategori->beli)
                                                <li>✓ Beli</li>
                                            @endif
                                            @if ($kategori->stok)
                                                <li>✓ Stok</li>
                                            @endif
                                            @if ($kategori->produksi)
                                                <li>✓ Produksi</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        <a href="{{ route('kategoriUtama.edit', $kategori) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        {{-- <form action="{{ route('produk-kategori-utama.destroy', $kategori) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                </form> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-scripts')
    <script>
        let table = new DataTable('#myTable');
    </script>
@endpush
