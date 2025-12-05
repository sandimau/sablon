@extends('layouts.app')

@section('title')
    Bayar Semua - {{ $freelance->nama }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Pembayaran Gaji Freelance</h5>
                    <h6 class="card-subtitle mb-2 text-muted">{{ $freelance->nama }} - {{ $namaBulan }} {{ $thn }}</h6>
                </div>
                <a href="{{ route('freelances.show', ['freelance' => $freelance->id, 'tab' => 'profil', 'bulan' => $bln, 'tahun' => $thn]) }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            @include('layouts.includes.messages')

            <form method="POST" action="{{ route('freelance.bayar_semua.store', $freelance->id) }}">
                @csrf
                <input type="hidden" name="tahun" value="{{ $thn }}">
                <input type="hidden" name="bulan" value="{{ $bln }}">

                <!-- Info Freelance -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bx bx-user"></i> Info Freelance</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="40%">Nama</td>
                                        <td><strong>{{ $freelance->nama }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Bank</td>
                                        <td>{{ $freelance->bank ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>No. Rekening</td>
                                        <td>{{ $freelance->nomor_rekening ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bx bx-calendar"></i> Periode</h6>
                                <h4 class="text-primary">{{ $namaBulan }} {{ $thn }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rincian Pembayaran -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bx bx-list-check"></i> Rincian Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Keterangan</th>
                                    <th class="text-end" width="200">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Upah</strong>
                                        @if($hutangUpah->count() > 0)
                                            <small class="text-muted d-block">
                                                {{ $hutangUpah->count() }} tagihan belum lunas
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($sisaUpah, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Lembur</strong>
                                        @if($hutangLembur->count() > 0)
                                            <small class="text-muted d-block">
                                                {{ $hutangLembur->count() }} tagihan belum lunas
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($sisaLembur, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="table-secondary">
                                    <td><strong>Sub Total</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($totalSebelumPotongan, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong class="text-danger">Potongan Kasbon</strong>
                                        <small class="text-muted d-block">Saldo kasbon: Rp {{ number_format($saldoKasbon, 0, ',', '.') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control text-end" name="potongan_kasbon" id="potongan_kasbon" 
                                                value="{{ $potonganKasbon }}" min="0" max="{{ $saldoKasbon }}"
                                                onchange="hitungTotal()">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong style="font-size: 1.1em;">💰 TOTAL DITERIMA</strong></td>
                                    <td class="text-end">
                                        <strong style="font-size: 1.2em;" id="totalDiterima">Rp {{ number_format($totalDiterima, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Pembayaran -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="tanggal" class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                            <input type="date" class="form-control {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" 
                                name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @if($errors->has('tanggal'))
                                <div class="invalid-feedback">{{ $errors->first('tanggal') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="akun_detail_id" class="form-label">Kas <span class="text-danger">*</span></label>
                            <select class="form-select {{ $errors->has('akun_detail_id') ? 'is-invalid' : '' }}" 
                                name="akun_detail_id" id="akun_detail_id" required>
                                <option value="">-- Pilih Kas --</option>
                                @foreach($kas as $id => $nama)
                                    <option value="{{ $id }}" {{ old('akun_detail_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('akun_detail_id'))
                                <div class="invalid-feedback">{{ $errors->first('akun_detail_id') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="keterangan" class="form-label">Keterangan (opsional)</label>
                    <textarea class="form-control" name="keterangan" id="keterangan" rows="2">{{ old('keterangan') }}</textarea>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('freelances.show', ['freelance' => $freelance->id, 'tab' => 'profil', 'bulan' => $bln, 'tahun' => $thn]) }}" class="btn btn-secondary">
                        <i class="bx bx-x"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success btn-lg" {{ $totalSebelumPotongan <= 0 ? 'disabled' : '' }}>
                        <i class="bx bx-check"></i> Proses Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const totalSebelumPotongan = {{ $totalSebelumPotongan }};
        const saldoKasbon = {{ $saldoKasbon }};

        function hitungTotal() {
            let potongan = parseInt(document.getElementById('potongan_kasbon').value) || 0;
            
            // Validasi potongan tidak melebihi saldo kasbon
            if (potongan > saldoKasbon) {
                potongan = saldoKasbon;
                document.getElementById('potongan_kasbon').value = saldoKasbon;
            }
            
            // Validasi potongan tidak melebihi total
            if (potongan > totalSebelumPotongan) {
                potongan = totalSebelumPotongan;
                document.getElementById('potongan_kasbon').value = totalSebelumPotongan;
            }

            const totalDiterima = totalSebelumPotongan - potongan;
            document.getElementById('totalDiterima').textContent = 'Rp ' + totalDiterima.toLocaleString('id-ID');
        }
    </script>
@endsection

