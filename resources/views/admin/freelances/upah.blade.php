@extends('layouts.app')

@section('title', 'Daftar Upah Freelancer')

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">Daftar Upah Freelance</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Filter Form -->
                <div class="mb-4">
                    <form action="{{ route('freelance.upah') }}" method="get" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="{{ $nama ?? '' }}" placeholder="Cari nama freelancer">
                        </div>
                        <div class="col-md-3">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="bulan" class="form-control">
                                <option value="01" {{ $bln == '01' ? 'selected' : '' }}>Januari</option>
                                <option value="02" {{ $bln == '02' ? 'selected' : '' }}>Februari</option>
                                <option value="03" {{ $bln == '03' ? 'selected' : '' }}>Maret</option>
                                <option value="04" {{ $bln == '04' ? 'selected' : '' }}>April</option>
                                <option value="05" {{ $bln == '05' ? 'selected' : '' }}>Mei</option>
                                <option value="06" {{ $bln == '06' ? 'selected' : '' }}>Juni</option>
                                <option value="07" {{ $bln == '07' ? 'selected' : '' }}>Juli</option>
                                <option value="08" {{ $bln == '08' ? 'selected' : '' }}>Agustus</option>
                                <option value="09" {{ $bln == '09' ? 'selected' : '' }}>September</option>
                                <option value="10" {{ $bln == '10' ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ $bln == '11' ? 'selected' : '' }}>November</option>
                                <option value="12" {{ $bln == '12' ? 'selected' : '' }}>Desember</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-control">
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ $thn == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status_bayar" class="form-label">Status Pembayaran</label>
                            <select name="status_bayar" id="status_bayar" class="form-control">
                                <option value="all" {{ $statusBayar == 'all' ? 'selected' : '' }}>Semua</option>
                                <option value="sudah" {{ $statusBayar == 'sudah' ? 'selected' : '' }}>Sudah Dibayar</option>
                                <option value="belum" {{ $statusBayar == 'belum' ? 'selected' : '' }}>Belum Dibayar</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-filter"></i> Filter
                            </button>
                            <a href="{{ route('freelance.upah') }}" class="btn btn-secondary">
                                <i class="bx bx-refresh"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Jumlah Upah</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Status Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hutang as $item)
                                <tr>
                                    <td>{{ $item->freelance->nama ?? '-' }}</td>
                                    <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                    <td>
                                        @if ($item->sisa <= 0)
                                            <span class="badge bg-success">Sudah Dibayar</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Dibayar</span>
                                        @endif
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
        $(document).ready(function() {
            let table = new DataTable('#myTable', {
                order: [[0, 'desc']],
                pageLength: 25,
                language: {
                    emptyTable: "Tidak ada data upah untuk periode yang dipilih."
                }
            });
        });
    </script>
@endpush
