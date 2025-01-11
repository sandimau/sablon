@extends('layouts.app')

@section('title')
    Data Member Ijin
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="mt-2">
            @include('layouts.includes.messages')
        </div>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Ijin</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your cutis here.</h6>
                    </div>
                    @can('cuti_create')
                        <a href="{{ route('ijin.create', $member->id) }}" class="btn btn-primary"><i class='bx bx-plus-circle'></i> tambah</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                {{ $cutis->links() }}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    tanggal
                                </th>
                                <th>
                                    keterangan
                                </th>
                                <th>
                                    ijin
                                </th>
                                <th>
                                    action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cutis as $item)
                                <tr>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->cuti ? 'cuti' : 'ijin' }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('cuti.edit', $item->id) }}" class="btn btn-info btn-sm me-1"><i
                                                    class='bx bxs-edit'></i>
                                                Edit</a>
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
