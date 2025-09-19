@extends('layouts.app')

@section('title', 'Ajukan Lembur Freelancer')

@section('content')
<div class="card">
    <div class="card-header">Ajukan Lembur</div>
    <div class="card-body">
        <form action="{{ route('freelance_overtime.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="freelance_id" class="form-label">Freelancer</label>
                <input type="text" class="form-control" value="{{ $freelance->nama ?? '' }}" readonly>
                <input type="hidden" class="form-control" id="freelance_id" name="freelance_id" value="{{ $freelance->id ?? '' }}">
            </div>

            <div class="mb-3">
                <label for="jam_lembur" class="form-label">Jam Lembur</label>
                <select name="jam_lembur" id="jam_lembur" class="form-control" required>
                    @for($i = 0.5; $i <= 6; $i += 0.5)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-success">Ajukan</button>
            <a href="{{ route('freelance_overtime.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection
