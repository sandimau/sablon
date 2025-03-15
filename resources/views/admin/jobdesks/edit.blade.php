@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Edit Jobdesk</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('jobdesks.update', $jobdesk) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror"
                    id="title" name="title" value="{{ old('title', $jobdesk->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role_id" class="form-label">Role</label>
                <select class="form-select @error('role_id') is-invalid @enderror"
                    id="role_id" name="role_id" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id', $jobdesk->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                    id="description" name="description" rows="5" required>{{ old('description', $jobdesk->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('jobdesks.index') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update Jobdesk</button>
            </div>
        </form>
    </div>
</div>
@endsection