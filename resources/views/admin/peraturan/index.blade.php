@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Peraturan Pegawai</h5>
            <a href="{{ route('peraturan.create') }}" class="btn btn-primary">Tambah Peraturan</a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>isi</th>
                        <th>Tanggal Berlaku</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peraturans as $peraturan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $peraturan->judul }}</td>
                        <td>{{ $peraturan->isi }}</td>
                        <td>{{ $peraturan->tanggal_berlaku->format('d/m/Y') }}</td>
                        <td>{{ $peraturan->status }}</td>
                        <td>
                            <a href="{{ route('peraturan.edit', $peraturan) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('peraturan.destroy', $peraturan) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
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