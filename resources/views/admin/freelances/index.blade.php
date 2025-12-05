@extends('layouts.app')

@section('title')
    Freelancer List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Freelance List</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage freelance here.</h6>
                    </div>
                    @can('freelance_create')
                        <a href="{{route('freelances.create')}}" class="btn btn-primary">Tambah Freelance</a>
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
                                <th scope="col">Nama</th>
                                <th scope="col">Upah</th>
                                <th scope="col">Lembur</th>
                                <th scope="col">absen Bulan Ini</th>
                                <th scope="col">Kasbon</th>
                                <th scope="col">Active</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($freelances as $freelance)
                                <tr>
                                    <td>
                                        @can('freelance_delete')
                                            <a href="{{ route('freelances.show', $freelance->id) }}">
                                                {{ $freelance->nama }}
                                            </a>
                                        @else
                                            {{ $freelance->nama }}
                                        @endcan
                                    </td>
                                    <td align="right">Rp {{number_format($freelance?->upah_belum_dibayar,2)}}</td>
                                    <td align="right">Rp {{number_format($freelance?->lembur_belum_dibayar,2)}}</td>
                                    <td align="center">{{$freelance?->total_kehadiran}} hari</td>
                                    <td align="right">Rp {{number_format($freelance?->kasbon_belum_lunas,2)}}</td>
                                    <td>
                                        <span class="badge {{ $freelance?->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $freelance?->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{route('freelances.edit',$freelance->id)}}" class="btn btn-info btn-sm me-1">
                                                <i class="bx bxs-edit"></i> Edit
                                            </a>
                                            <form action="{{route('freelances.destroy',$freelance->id)}}" method="POST">
                                                {{csrf_field()}}
                                                {{method_field('delete')}}
                                                <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Apakah anda yakin akan menghapus data ini?')">
                                                    <i class="bx bxs-trash"></i> Delete
                                                </button>
                                            </form>
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

@push('after-scripts')
<script>
    let table = new DataTable('#myTable');
</script>
@endpush
