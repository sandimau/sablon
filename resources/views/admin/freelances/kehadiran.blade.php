@extends('layouts.app')

@section('title')
    Data Kehadiran
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Data Kehadiran</h5>
                    </div>
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
                                <th scope="col">ID</th>
                                <th scope="col">NIK</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Type</th>
                                <th scope="col">Status Freelance</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataAbsensi as $item)
                                @if($item['found_in_freelance'])
                                    <tr>
                                        <td>{{ $item['absensi']->id }}</td>
                                        <td>{{ $item['absensi']->nik }}</td>
                                        <td>{{ $item['absensi']->name }}</td>
                                        <td>{{ $item['absensi']->tanggal ? \Carbon\Carbon::parse($item['absensi']->tanggal)->format('Y-m-d H:i:s') : '-' }}</td>
                                        <td>{{ $item['absensi']->type }}</td>
                                        <td>
                                            <span class="badge bg-success">Terdaftar sebagai Freelance</span>
                                        </td>
                                        <td>
                                            @if($item['freelance_data'])
                                                <a href="{{ url('admin/freelance_overtime') }}?nama={{ urlencode($item['freelance_data']->nama) }}&bulan={{ date('m') }}&tahun={{ date('Y') }}&status_bayar=all" class="btn btn-info btn-sm">
                                                    <i class="bx bxs-user"></i> Detail
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
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
    $(document).ready(function() {
        let table = new DataTable('#myTable', {
            order: [[3, 'desc']],
            pageLength: 25,
            language: {
                emptyTable: "Tidak ada tagihan untuk periode yang dipilih."
            }
        });
    });
</script>
@endpush
