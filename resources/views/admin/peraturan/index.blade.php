@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Peraturan Pegawai</h5>
                @role('super')
                    <a href="{{ route('peraturan.create') }}" class="btn btn-primary">Tambah Peraturan</a>
                @endrole
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>isi</th>
                            @role('super')
                                <th>Aksi</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peraturans as $peraturan)
                            <tr>
                                <td>{{ $peraturan->judul }}</td>
                                <td>{!! $peraturan->isi !!}</td>
                                @role('super')
                                    <td>
                                        <a href="{{ route('peraturan.edit', $peraturan) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('peraturan.destroy', $peraturan) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                        </form>
                                    </td>
                                @endrole
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
