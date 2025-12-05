@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                {{-- Navigation Tabs --}}
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('hutang.belumLunas') }}">Belum Lunas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('hutang.sudahLunas') }}">Sudah Lunas</a>
                    </li>
                </ul>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Hutang Piutang - Sudah Lunas</h5>
                                <h6 class="card-subtitle mb-2 text-muted">Daftar hutang/piutang yang sudah lunas.</h6>
                            </div>
                            <div>
                                <a href="{{ route('hutang.create', ['jenis' => 'hutang']) }}" class="btn btn-primary">Hutang
                                    Baru</a>
                                <a href="{{ route('hutang.create', ['jenis' => 'piutang']) }}"
                                    class="btn btn-primary">Piutang Baru</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        {{-- Filter by Jenis --}}
                        <div class="mb-3">
                            <form action="{{ route('hutang.sudahLunas') }}" method="GET" class="d-flex align-items-center gap-2">
                                <label for="jenis" class="form-label mb-0 me-2">Filter Jenis:</label>
                                <select name="jenis" id="jenis" class="form-select" style="width: auto;" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    <option value="hutang" {{ ($jenisFilter ?? '') == 'hutang' ? 'selected' : '' }}>Hutang</option>
                                    <option value="piutang" {{ ($jenisFilter ?? '') == 'piutang' ? 'selected' : '' }}>Piutang</option>
                                    <option value="lembur" {{ ($jenisFilter ?? '') == 'lembur' ? 'selected' : '' }}>Lembur</option>
                                    <option value="upah" {{ ($jenisFilter ?? '') == 'upah' ? 'selected' : '' }}>Upah</option>
                                </select>
                                @if($jenisFilter ?? null)
                                    <a href="{{ route('hutang.sudahLunas') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                @endif
                            </form>
                        </div>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kontak</th>
                                    <th>Jumlah</th>
                                    <th>Jenis</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hutangs as $hutang)
                                    <tr>
                                        <td>{{ $hutang->tanggal->format('d/m/Y') }}</td>
                                        <td>
                                            @if (in_array($hutang->jenis, ['lembur', 'upah']))
                                                @if ($hutang->freelance)
                                                    {{ $hutang->freelance->nama }}
                                                @elseif ($hutang->kontak)
                                                    {{ $hutang->kontak->nama }}
                                                @else
                                                    -
                                                @endif
                                            @else
                                                @if ($hutang->kontak)
                                                    {{ $hutang->kontak->nama }}
                                                @else
                                                    -
                                                @endif
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($hutang->jumlah, 0, ',', '.') }}</td>
                                        <td>{{ ucfirst($hutang->jenis) }}</td>
                                        <td>
                                            <a href="{{ route('hutang.detail', $hutang) }}"
                                                class="btn btn-sm btn-success">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $hutangs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

