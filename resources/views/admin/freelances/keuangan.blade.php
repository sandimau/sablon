@extends('layouts.app')

@section('title')
    Tagihan Upah & Lembur
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Tagihan Upah & Lembur</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>

                <!-- Filter Form -->
                <div class="mb-4">
                    <form action="{{ route('freelance.keuangan') }}" method="get" class="row g-3 align-items-end">
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-control">
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ $thn == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="kontak_id" class="form-label">Nama Freelance</label>
                            <select name="kontak_id" id="kontak_id" class="form-control">
                                <option value="">Semua Freelance</option>
                                @foreach ($all_freelances as $freelance)
                                    <option value="{{ $freelance['id'] }}" {{ (isset($kontak_id) && $kontak_id == $freelance['id']) ? 'selected' : '' }}>
                                        {{ $freelance['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="jenis" class="form-label">Jenis</label>
                            <select name="jenis" id="jenis" class="form-control">
                                <option value="">Semua Jenis</option>
                                <option value="upah" {{ (isset($jenis_filter) && $jenis_filter == 'upah') ? 'selected' : '' }}>Upah</option>
                                <option value="lembur" {{ (isset($jenis_filter) && $jenis_filter == 'lembur') ? 'selected' : '' }}>Lembur</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-filter"></i> Filter
                            </button>
                            <a href="{{ route('freelance.keuangan') }}" class="btn btn-secondary">
                                <i class="bx bx-refresh"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary">Total Tagihan</h6>
                                <h4 class="mb-0">Rp {{ number_format($total_tagihan, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6 class="card-title text-success">Total Dibayar</h6>
                                <h4 class="mb-0">Rp {{ number_format($total_bayar, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h6 class="card-title text-danger">Total Belum Dibayar</h6>
                                <h4 class="mb-0">Rp {{ number_format($total_sisa, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="myTable">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Nama Freelance</th>
                                <th scope="col">Jenis</th>
                                <th scope="col">Jumlah Tagihan</th>
                                <th scope="col">Total Dibayar</th>
                                <th scope="col">Sisa</th>
                                <th scope="col">Status</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tagihans as $tagihan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $tagihan->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($tagihan->freelance)
                                            @if ($tagihan->jenis == 'upah')
                                                <a href="{{ route('freelance.upah') }}">
                                                    {{ $tagihan->freelance->nama }}
                                                </a>
                                            @else
                                                <a href="{{ route('freelance_overtime.index') }}">
                                                    {{ $tagihan->freelance->nama }}
                                                </a>
                                            @endif
                                        @elseif ($tagihan->kontak)
                                            {{ $tagihan->kontak->nama }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $tagihan->jenis == 'upah' ? 'bg-info' : 'bg-secondary' }}">
                                            {{ ucfirst($tagihan->jenis) }}
                                        </span>
                                    </td>
                                    <td align="right">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                                    <td align="right">Rp {{ number_format($tagihan->total_bayar, 0, ',', '.') }}</td>
                                    <td align="right">Rp {{ number_format($tagihan->sisa, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($tagihan->status == 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-warning">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td>{{ $tagihan->keterangan ?? '-' }}</td>
                                    <td>
                                        @if ($tagihan->sisa > 0)
                                            <a href="{{ route('hutang.bayar', $tagihan) }}" class="btn btn-sm btn-warning" title="Bayar">
                                                <i class="bx bx-money"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('hutang.detail', $tagihan) }}" class="btn btn-sm btn-info" title="Detail">
                                            <i class="bx bx-detail"></i>
                                        </a>
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
            order: [[1, 'desc']],
            pageLength: 25,
            columnDefs: [
                { orderable: false, targets: [0, 9] } // Disable sorting on No and Actions columns
            ],
            language: {
                emptyTable: "Tidak ada tagihan untuk periode yang dipilih."
            }
        });
    });
</script>
@endpush

