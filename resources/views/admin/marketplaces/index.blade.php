@extends('layouts.app')

@section('title')
    Marketplace List
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Marketplace</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your marketplace here.</h6>
                    </div>
                    @can('marketplace_create')
                        <a href="{{ route('marketplaces.create') }}" class="btn btn-primary ">Add marketplace</a>
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
                                <th scope="col">nama</th>
                                <th scope="col">marketplace</th>
                                <th scope="col">kas marketplace</th>
                                <th scope="col">kas penarikan</th>
                                <th scope="col">konsumen</th>
                                <th scope="col">actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($marketplaces as $marketplace)
                                <tr>
                                    <td><a href="{{ route('marketplaces.show', $marketplace->id) }}">{{ $marketplace->nama }}</a></td>
                                    <td>{{ $marketplace->marketplace }}</td>
                                    <td>{{ $marketplace->kas->nama }}</td>
                                    <td>{{ $marketplace->kasPenarikan->nama }}</td>
                                    <td>{{ $marketplace->kontak->nama }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('marketplaces.edit', $marketplace->id) }}" class="btn btn-info btn-sm me-1"><i
                                                    class='bx bxs-edit'></i> Edit</a>
                                            <a href="{{ route('marketplaces.destroy', $marketplace->id) }}" class="btn btn-danger btn-sm me-1"><i
                                                    class='bx bxs-trash'></i> Delete</a>
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
