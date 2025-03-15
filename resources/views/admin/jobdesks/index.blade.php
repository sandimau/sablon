@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Jobdesk Pegawai</h5>
            <a href="{{ route('jobdesks.create') }}" class="btn btn-primary">Tambah</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Role</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobdesks as $jobdesk)
                            <tr>
                                <td>{{ $jobdesk->title }}</td>
                                <td>{{ $jobdesk->role->name }}</td>
                                <td>{{ $jobdesk->description }}</td>
                                <td>
                                    <a href="{{ route('jobdesks.edit', $jobdesk) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ route('jobdesks.destroy', $jobdesk) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
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