@extends('layouts.app')

@section('title')
    Kategori List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Kategoris</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your Kategoris here.</h6>
                    </div>
                    @can('akun_create')
                        <a href="{{ route('kategori.createByKategoriUtama', $kategoriUtama->id) }}" class="btn btn-primary ">Add Kategori</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama</th>
                                <th scope="col">actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategoris as $index => $kategori)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><a href="{{ route('produkModel.index', $kategori->id) }}">{{ $kategori->nama }}</a></td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('kategori.edit', $kategori->id) }}" class="btn btn-info btn-sm me-1"><i
                                                    class='bx bxs-edit'></i> Edit</a>
                                        </div>
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
