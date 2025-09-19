@extends('layouts.app')

@section('title', 'Daftar Lembur Freelancer')

@section('content')
<div class="bg-light rounded">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">Daftar Lembur Freelancer</h4>
                </div>
                <div>
                    <a href="{{ route('freelance_overtime.create') }}" class="btn btn-primary btn-sm">+ Ajukan Lembur</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped" id="myTable">
                    <thead>
                        <tr>
                            <th scope="col">Pengajuan</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Jam Lembur</th>
                            <th scope="col">Jumlah Upah</th>
                            <th scope="col">Kas</th>
                            <th scope="col">Status</th>
                            <th scope="col">Catatan Akunting</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overtimes as $overtime)
                            <tr>
                                <td>{{ $overtime->created_at->format('d M Y') }}</td>
                                <td>{{ $overtime->freelance->nama }}</td>
                                <td>
                                    @if($overtime->kategori == 456)
                                        Lembur Printing
                                    @elseif($overtime->kategori == 464)
                                        Lembur Sublime
                                    @else
                                        -
                                    @endif
                                <td>{{ $overtime->jam_lembur }}</td>
                                <td>Rp {{ number_format($overtime->jumlah_upah, 0, ',', '.') }}</td>
                                <td>{{ $overtime->akunDetail ? $overtime->akunDetail->nama : '-' }}</td>
                                <td>
                                    @if($overtime->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($overtime->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $overtime->catatan_akunting ?? '-' }}</td>
                                <td>
                                    @if ($overtime->status == 'approved')
                                        <button class="btn btn-sm btn-info" disabled>Edit</button>
                                    @else
                                        <a href="{{ route('freelance_overtime.edit', $overtime->id) }}" class="btn btn-sm btn-info">Edit</a>
                                    @endif
                                    <form action="{{ route('freelance_overtime.destroy', $overtime->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin mau hapus?')">Hapus</button>
                                    </form>
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