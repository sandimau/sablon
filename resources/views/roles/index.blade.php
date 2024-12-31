@extends('layouts.app')

@section('title')
    Role list
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Roles</h5>
                        <h6 class="card-subtitle mb-2 text-muted"> Manage your roles here.</h6>
                    </div>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">Add role</a>
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
                                <th>No</th>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @foreach ($role->permissions as $perm)
                                            <span class="badge text-bg-info">{{ $perm->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('roles.show', $role->id) }}"
                                                class="btn btn-warning btn-sm me-1"><i class='bx bx-plus-circle'></i> Show</a>
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info btn-sm me-1"><i
                                                    class='bx bxs-edit'></i> Edit</a>
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="btn btn-danger btn-sm"><i class='bx bxs-trash'></i> delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="d-flex">
                    {!! $roles->links() !!}
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
