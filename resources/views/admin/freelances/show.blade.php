@extends('layouts.app')

@section('title')
    Detail Freelance - {{ $freelance->nama }}
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Detail Freelance</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ $freelance->nama }}</h6>
                    </div>
                    <div>
                        <a href="{{ route('freelances.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                        @can('freelance_edit')
                            <a href="{{ route('freelances.edit', $freelance->id) }}" class="btn btn-primary">
                                <i class="bx bxs-edit"></i> Edit
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-4" id="freelanceTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tab == 'profil' ? 'active' : '' }}"
                           href="{{ route('freelances.show', $freelance->id) }}?tab=profil&bulan={{ $bln }}&tahun={{ $thn }}"
                           role="tab">
                            <i class="bx bx-user"></i> Profil
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tab == 'upah' ? 'active' : '' }}"
                           href="{{ route('freelances.show', $freelance->id) }}?tab=upah&bulan={{ $bln }}&tahun={{ $thn }}"
                           role="tab">
                            <i class="bx bx-money"></i> Upah
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tab == 'lembur' ? 'active' : '' }}"
                           href="{{ route('freelances.show', $freelance->id) }}?tab=lembur&bulan={{ $bln }}&tahun={{ $thn }}"
                           role="tab">
                            <i class="bx bx-time"></i> Lembur
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tab == 'kehadiran' ? 'active' : '' }}"
                           href="{{ route('freelances.show', $freelance->id) }}?tab=kehadiran&bulan={{ $bln }}&tahun={{ $thn }}"
                           role="tab">
                            <i class="bx bx-calendar-check"></i> Kehadiran
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $tab == 'kasbon' ? 'active' : '' }}"
                           href="{{ route('freelances.show', $freelance->id) }}?tab=kasbon&bulan={{ $bln }}&tahun={{ $thn }}"
                           role="tab">
                            <i class="bx bx-credit-card"></i> Kasbon
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="freelanceTabContent">

                    @if($tab == 'profil')
                    <!-- Tab Profil -->
                    <div class="tab-pane fade show active" id="profil" role="tabpanel">
                        <!-- Filter Form -->
                        <div class="mb-4">
                            <form action="{{ route('freelances.show', $freelance->id) }}" method="get" class="row g-3 align-items-end">
                                <input type="hidden" name="tab" value="profil">
                                <div class="col-md-2">
                                    <label for="bulan" class="form-label">Bulan</label>
                                    <select name="bulan" id="bulan" class="form-control">
                                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $key => $value)
                                            <option value="{{ $key }}" {{ $bln == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-filter"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bx bx-id-card"></i> Informasi Pribadi</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Nama</th>
                                                <td>{{ $freelance->nama }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alamat</th>
                                                <td>{{ $freelance->alamat ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Handphone</th>
                                                <td>{{ $freelance->handphone ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Masuk</th>
                                                <td>{{ $freelance->tanggal_masuk ? $freelance->tanggal_masuk->format('d F Y') : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span class="badge {{ $freelance->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $freelance->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bx bx-wallet"></i> Informasi Keuangan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">Upah</th>
                                                <td>Rp {{ number_format($freelance->upah, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Rate Lembur/Jam</th>
                                                <td>Rp {{ number_format($freelance->rate_lembur_per_jam, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Bank</th>
                                                <td>{{ $freelance->bank ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nomor Rekening</th>
                                                <td>{{ $freelance->nomor_rekening ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Bulan Ini -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="mb-3">Ringkasan Bulan {{ \Carbon\Carbon::create()->month($bln)->translatedFormat('F') }} {{ $thn }}</h6>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-primary">Total Upah</h6>
                                        <h5 class="mb-0">Rp {{ number_format($totalUpah, 0, ',', '.') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-info">Total Lembur</h6>
                                        <h5 class="mb-0">Rp {{ number_format($totalLembur, 0, ',', '.') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-success">Dibayar (Upah)</h6>
                                        <h5 class="mb-0">Rp {{ number_format($totalBayarUpah, 0, ',', '.') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-warning">Dibayar (Lembur)</h6>
                                        <h5 class="mb-0">Rp {{ number_format($totalBayarLembur, 0, ',', '.') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Pembayaran & Tombol Bayar -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bx bx-wallet"></i> Status Pembayaran</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <tr>
                                                        <td>Sisa Upah Belum Dibayar</td>
                                                        <td class="text-end {{ $sisaUpah > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                                            Rp {{ number_format($sisaUpah, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sisa Lembur Belum Dibayar</td>
                                                        <td class="text-end {{ $sisaLembur > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                                            Rp {{ number_format($sisaLembur, 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    <tr class="border-top">
                                                        <td><strong>Total Belum Dibayar</strong></td>
                                                        <td class="text-end {{ ($sisaUpah + $sisaLembur) > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                                            <strong>Rp {{ number_format($sisaUpah + $sisaLembur, 0, ',', '.') }}</strong>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-7 text-end">
                                                {{-- Tombol Bayar Semua --}}
                                                @if($sisaUpah > 0 || $sisaLembur > 0)
                                                    <a href="{{ route('freelance.bayar_semua', ['freelance' => $freelance->id, 'bulan' => $bln, 'tahun' => $thn]) }}" class="btn btn-success me-1 mb-2">
                                                        <i class="bx bx-money"></i> Bayar Semua (Rp {{ number_format($sisaUpah + $sisaLembur, 0, ',', '.') }})
                                                    </a>
                                                @endif

                                                {{-- Tombol Print Slip --}}
                                                @if($sudahDibayar)
                                                    <a href="{{ route('freelance.slip', ['freelance' => $freelance->id, 'bulan' => $bln, 'tahun' => $thn]) }}" class="btn btn-primary mb-2" target="_blank">
                                                        <i class="bx bx-printer"></i> Print Slip Pembayaran
                                                    </a>
                                                @endif

                                                {{-- Jika semua sudah lunas --}}
                                                @if($sisaUpah == 0 && $sisaLembur == 0 && ($totalUpah > 0 || $totalLembur > 0))
                                                    <span class="badge bg-success p-2 mb-2">
                                                        <i class="bx bx-check-circle"></i> Semua Sudah Lunas
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($tab == 'upah')
                    <!-- Tab Upah -->
                    <div class="tab-pane fade show active" id="upah" role="tabpanel">
                        <!-- Filter Form -->
                        <div class="mb-4">
                            <form action="{{ route('freelances.show', $freelance->id) }}" method="get" class="row g-3 align-items-end">
                                <input type="hidden" name="tab" value="upah">
                                <div class="col-md-2">
                                    <label for="bulan" class="form-label">Bulan</label>
                                    <select name="bulan" id="bulan" class="form-control">
                                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $key => $value)
                                            <option value="{{ $key }}" {{ $bln == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-filter"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tableUpah">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah Tagihan</th>
                                        <th>Total Dibayar</th>
                                        <th>Sisa</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hutangUpah as $hutang)
                                        @php
                                            $totalBayar = $hutang->details->sum('jumlah');
                                            $sisa = $hutang->jumlah - $totalBayar;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $hutang->tanggal->format('d/m/Y') }}</td>
                                            <td align="right">Rp {{ number_format($hutang->jumlah, 0, ',', '.') }}</td>
                                            <td align="right">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                                            <td align="right">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                            <td>
                                                @if($sisa <= 0)
                                                    <span class="badge bg-success">Lunas</span>
                                                @else
                                                    <span class="badge bg-warning">Belum Lunas</span>
                                                @endif
                                            </td>
                                            <td>{{ $hutang->keterangan ?? '-' }}</td>
                                            <td>
                                                @if($sisa > 0)
                                                    <a href="{{ route('hutang.bayar', $hutang) }}" class="btn btn-sm btn-warning" title="Bayar">
                                                        <i class="bx bx-money"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('hutang.detail', $hutang) }}" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bx bx-detail"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($tab == 'lembur')
                    <!-- Tab Lembur -->
                    <div class="tab-pane fade show active" id="lembur" role="tabpanel">
                        <!-- Filter Form -->
                        <div class="mb-4">
                            <form action="{{ route('freelances.show', $freelance->id) }}" method="get" class="row g-3 align-items-end">
                                <input type="hidden" name="tab" value="lembur">
                                <div class="col-md-2">
                                    <label for="bulan" class="form-label">Bulan</label>
                                    <select name="bulan" id="bulan" class="form-control">
                                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $key => $value)
                                            <option value="{{ $key }}" {{ $bln == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-filter"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tableLembur">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah Tagihan</th>
                                        <th>Total Dibayar</th>
                                        <th>Sisa</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hutangLembur as $hutang)
                                        @php
                                            $totalBayar = $hutang->details->sum('jumlah');
                                            $sisa = $hutang->jumlah - $totalBayar;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $hutang->tanggal->format('d/m/Y') }}</td>
                                            <td align="right">Rp {{ number_format($hutang->jumlah, 0, ',', '.') }}</td>
                                            <td align="right">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                                            <td align="right">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                            <td>
                                                @if($sisa <= 0)
                                                    <span class="badge bg-success">Lunas</span>
                                                @else
                                                    <span class="badge bg-warning">Belum Lunas</span>
                                                @endif
                                            </td>
                                            <td>{{ $hutang->keterangan ?? '-' }}</td>
                                            <td>
                                                @if($sisa > 0)
                                                    <a href="{{ route('hutang.bayar', $hutang) }}" class="btn btn-sm btn-warning" title="Bayar">
                                                        <i class="bx bx-money"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('hutang.detail', $hutang) }}" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bx bx-detail"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($tab == 'kehadiran')
                    <!-- Tab Kehadiran -->
                    <div class="tab-pane fade show active" id="kehadiran" role="tabpanel">
                        <!-- Filter Form -->
                        <div class="mb-4">
                            <form action="{{ route('freelances.show', $freelance->id) }}" method="get" class="row g-3 align-items-end">
                                <input type="hidden" name="tab" value="kehadiran">
                                <div class="col-md-2">
                                    <label for="bulan" class="form-label">Bulan</label>
                                    <select name="bulan" id="bulan" class="form-control">
                                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $key => $value)
                                            <option value="{{ $key }}" {{ $bln == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-filter"></i> Filter
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Summary -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-success">Total Kehadiran</h6>
                                        <h4 class="mb-0">{{ $absensis->count() }} Hari</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tableKehadiran">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal & Waktu</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($absensis as $absen)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $absen->tanggal }}</td>
                                            <td>{{ $absen->nik ?? '-' }}</td>
                                            <td>{{ $absen->name }}</td>
                                            <td>{{ $absen->type ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($tab == 'kasbon')
                    <!-- Tab Kasbon -->
                    <div class="tab-pane fade show active" id="kasbon" role="tabpanel">
                        <div class="card-header mb-4">
                            @can('kasbon_create')
                                <a href="{{ route('freelance_kasbon.create', $freelance->id) }}" class="btn btn-success text-white me-1">
                                    <i class="bx bxs-plus-circle"></i> Tambah Kasbon
                                </a>
                                @if($saldoKasbon > 0)
                                    <a href="{{ route('freelance_kasbon.bayar', $freelance->id) }}" class="btn btn-primary text-white me-1">
                                        <i class="bx bx-money"></i> Bayar Kasbon
                                    </a>
                                @endif
                            @endcan
                        </div>

                        <!-- Summary -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card border-{{ $saldoKasbon > 0 ? 'danger' : 'success' }}">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-{{ $saldoKasbon > 0 ? 'danger' : 'success' }}">Saldo Kasbon</h6>
                                        <h4 class="mb-0">Rp {{ number_format($saldoKasbon, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tableKasbon">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Kasbon</th>
                                        <th>Pembayaran</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kasbons as $kasbon)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kasbon->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $kasbon->keterangan ?? '-' }}</td>
                                            <td align="right">Rp {{ number_format($kasbon->pemasukan ?? 0, 0, ',', '.') }}</td>
                                            <td align="right">Rp {{ number_format($kasbon->pengeluaran ?? 0, 0, ',', '.') }}</td>
                                            <td align="right">Rp {{ number_format($kasbon->saldo ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
<script>
    $(document).ready(function() {
        @if($tab == 'upah')
            new DataTable('#tableUpah', {
                order: [[1, 'desc']],
                pageLength: 25,
                language: {
                    emptyTable: "Tidak ada data upah untuk periode ini."
                }
            });
        @elseif($tab == 'lembur')
            new DataTable('#tableLembur', {
                order: [[1, 'desc']],
                pageLength: 25,
                language: {
                    emptyTable: "Tidak ada data lembur untuk periode ini."
                }
            });
        @elseif($tab == 'kehadiran')
            new DataTable('#tableKehadiran', {
                order: [[1, 'desc']],
                pageLength: 25,
                language: {
                    emptyTable: "Tidak ada data kehadiran untuk periode ini."
                }
            });
        @elseif($tab == 'kasbon')
            new DataTable('#tableKasbon', {
                order: [[1, 'desc']],
                pageLength: 25,
                language: {
                    emptyTable: "Tidak ada data kasbon."
                }
            });
        @endif
    });
</script>
@endpush

