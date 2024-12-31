@extends('layouts.app')

@section('title')
    Detail Member
@endsection

@section('content')
    <ul class="travel-tab nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="information-tab" data-bs-toggle="tab" data-bs-target="#information"
                type="button" role="tab" aria-controls="information" aria-selected="true">Detail</button>
        </li>
        @if ($member->status != 0)
            @can('member_show')
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button"
                        role="tab" aria-controls="user" aria-selected="false">User</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cuti-tab" data-bs-toggle="tab" data-bs-target="#cuti" type="button"
                        role="tab" aria-controls="cuti" aria-selected="false">Cuti/Ijin</button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lembur-tab" data-bs-toggle="tab" data-bs-target="#lembur" type="button"
                        role="tab" aria-controls="lembur" aria-selected="false">Lembur</button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="kasbon-tab" data-bs-toggle="tab" data-bs-target="#kasbon" type="button"
                        role="tab" aria-controls="kasbon" aria-selected="false">Kasbon</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tunjangan-tab" data-bs-toggle="tab" data-bs-target="#tunjangan" type="button"
                        role="tab" aria-controls="tunjangan" aria-selected="false">Tunjangan</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="gaji-tab" data-bs-toggle="tab" data-bs-target="#gaji" type="button"
                        role="tab" aria-controls="gaji" aria-selected="false">Format Gaji</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="penggajian-tab" data-bs-toggle="tab" data-bs-target="#penggajian"
                        type="button" role="tab" aria-controls="penggajian" aria-selected="false">Penggajian</button>
                </li>
            @endcan
        @endif
    </ul>

    <div class="mt-2">
        @include('layouts.includes.messages')
    </div>

    <div class="tab-content" id="myTabContent">
        <!-- start information -->
        <div class="tab-pane fade show active" id="information" role="tabpanel" aria-labelledby="information-tab">
            <div class="tab-content">
                <div class="card mt-4">
                    <div class="card-header">
                        @if ($member->status == 0)
                            <a class="btn btn-primary" href="{{ route('members.nonaktif') }}">
                                <i class='bx bx-arrow-back'></i> back
                            </a>
                        @else
                            <a class="btn btn-primary" href="{{ route('members.index') }}">
                                <i class='bx bx-arrow-back'></i> back
                            </a>
                        @endif
                        <a class="btn btn-warning" href="{{ route('members.edit', $member->id) }}">
                            <i class='bx bxs-edit'></i> edit
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <td>{{ $member->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Masuk</th>
                                        <td>{{ $member->tgl_masuk }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Keluar</th>
                                        <td>{{ $member->tgl_keluar }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Lahir</th>
                                        <td>{{ $member->tgl_lahir }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tempat Lahir</th>
                                        <td>{{ $member->tempat_lahir }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $member->alamat }}</td>
                                    </tr>
                                    <tr>
                                        <th>No Telp</th>
                                        <td>{{ $member->no_telp }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Gajian</th>
                                        <td>{{ date('d', strtotime($member->tgl_gajian)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>No Rek</th>
                                        <td>{{ $member->no_rek }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            {{ $member->status == 1 ? 'aktif' : '' }}
                                            {{ $member->status == 0 ? 'non aktif' : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>User Name</th>
                                        <td>{{ $member->user->name ?? '' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end information -->

        <!-- start tour user -->
        <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">
            <div class="tab-content mt-4">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class=" table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            email
                                        </th>
                                        <th>
                                            action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @if ($member->user_id)
                                            <td>
                                                {{ $member->user()->first()->email }}
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('users.edit', $member->user()->first()->id) }}"
                                                        class="btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i>
                                                        Edit</a>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tour user -->

        <!-- start tour cuti -->
        <div class="tab-pane fade" id="cuti" role="tabpanel" aria-labelledby="cuti-tab">
            <div class="tab-content">
                <div class="card mt-4">
                    <div class="card-header">
                        <a href="{{ route('cuti.create', $member->id) }}" class="btn btn-success text-white me-1"><i
                                class='bx bxs-edit'></i> add cuti</a>
                    </div>
                    <div class="card-body">
                        {{ $gajis->links() }}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" >
                                <thead>
                                    <tr>
                                        <th>
                                            tanggal
                                        </th>
                                        <th>
                                            keterangan
                                        </th>
                                        <th>
                                            cuti/ijin
                                        </th>
                                        <th>
                                            action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cutis as $item)
                                        <tr>
                                            <td>{{ $item->tanggal }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                            <td>{{ $item->cuti ? 'cuti' : 'ijin' }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('cuti.edit', $item->id) }}"
                                                        class="btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i>
                                                        Edit</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tour cuti -->

        <!-- start tour lembur -->
        <div class="tab-pane fade" id="lembur" role="tabpanel" aria-labelledby="lembur-tab">
            <div class="tab-content">
                <div class="card mt-4">
                    <div class="card-header">
                        <a href="{{ route('lembur.create', $member->id) }}" class="btn btn-success text-white me-1"><i
                                class='bx bxs-edit'></i> add lembur</a>
                    </div>
                    <div class="card-body">
                        {{ $gajis->links() }}
                        <div class="table-responsive">
                            <table class=" table table-bordered table-striped table-hover" >
                                <thead>
                                    <tr>
                                        <th>
                                            Tahun
                                        </th>
                                        <th>
                                            bulan
                                        </th>
                                        <th>
                                            jam
                                        </th>
                                        <th>
                                            keterangan
                                        </th>
                                        <th>
                                            dibayar
                                        </th>
                                        <th>
                                            actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lemburs as $item)
                                        <tr>
                                            <td>{{ $item->tahun }}</td>
                                            <td>{{ $item->bulan }}</td>
                                            <td>{{ $item->jam }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                            <td>{{ $item->dibayar }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ route('lembur.edit', $item->id) }}"
                                                        class="btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i>
                                                        Edit</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tour lembur -->

        <!-- start tour kasbon -->
        <div class="tab-pane fade" id="kasbon" role="tabpanel" aria-labelledby="kasbon-tab">
            <div class="tab-content">
                <div class="card mt-4">
                    <div class="card-header">
                        @can('kasbon_create')
                            <a href="{{ route('kasbon.create', $member->id) }}" class="btn btn-success text-white me-1"><i
                                    class='bx bxs-edit'></i> tambah kasbon</a>
                            @if (!$member->kasbon())
                                @if ($member->kasbon()->latest('id')->first()->saldo > 0)
                                    <a href="{{ route('kasbon.bayar', $member->id) }}"
                                        class="btn btn-primary text-white me-1"><i class='bx bxs-edit'></i> bayar kasbon</a>
                                @endif
                            @endif
                        @endcan
                    </div>
                    <div class="card-body">
                        {{ $gajis->links() }}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" >
                                <thead>
                                    <tr>
                                        <th>
                                            Tanggal
                                        </th>
                                        <th>
                                            ket
                                        </th>
                                        <th>
                                            kasbon
                                        </th>
                                        <th>
                                            pembayaran
                                        </th>
                                        <th>
                                            saldo
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kasbons as $item)
                                        <tr>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->keterangan }}</td>
                                            <td>{{ number_format($item->pemasukan) }}</td>
                                            <td>{{ number_format($item->pengeluaran) }}</td>
                                            <td>{{ number_format($item->saldo) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tour kasbon -->

        <!-- start tour tunjangan -->
        <div class="tab-pane fade" id="tunjangan" role="tabpanel" aria-labelledby="tunjangan-tab">
            <div class="tab-content">
                <div class="card mt-4">
                    <div class="card-header">
                        <a href="{{ route('tunjangan.create', $member->id) }}" class="btn btn-success text-white me-1"><i
                                class='bx bxs-edit'></i> add tunjangan</a>
                    </div>
                    <div class="card-body">
                        {{ $gajis->links() }}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" >
                                <thead>
                                    <tr>
                                        <th>
                                            Tanggal
                                        </th>
                                        <th>
                                            ket
                                        </th>
                                        <th>
                                            jumlah
                                        </th>
                                        <th>
                                            saldo
                                        </th>
                                        <th>
                                            kas
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tunjangans as $item)
                                        <tr>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->ket }}</td>
                                            <td>{{ number_format($item->jumlah) }}</td>
                                            <td>{{ number_format($item->saldo) }}</td>
                                            <td>{{ $item->akunDetail->nama }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tour tunjangan -->

        <!-- start tour gaji -->
        <div class="tab-pane fade" id="gaji" role="tabpanel" aria-labelledby="gaji-tab">
            <div class="tab-content">
                <div class="card mt-4">
                    <div class="card-header">
                        <a href="{{ route('gaji.create', $member->id) }}" class="btn btn-success text-white me-1"><i
                                class='bx bxs-edit'></i> add gaji</a>
                    </div>
                    <div class="card-body">
                        {{ $gajis->links() }}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            Tanggal
                                        </th>
                                        <th>
                                            Bagian
                                        </th>
                                        <th>
                                            Level
                                        </th>
                                        <th>
                                            Performance
                                        </th>
                                        <th>
                                            Transportasi
                                        </th>
                                        <th>
                                            Tunjangan Lain
                                        </th>
                                        <th>
                                            Nilai Tunjangan Lain
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gajis as $item)
                                        <tr>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->bagian->nama }}</td>
                                            <td>{{ $item->level->nama }}</td>
                                            <td>{{ $item->performance }}</td>
                                            <td>{{ $item->transportasi == 1 ? 'ya' : 'tidak' }}</td>
                                            <td>{{ $item->lain_lain }}</td>
                                            <td>{{ $item->jumlah_lain }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tour gaji -->

        <!-- start tour penggajian -->
        <div class="tab-pane fade" id="penggajian" role="tabpanel" aria-labelledby="penggajian-tab">
            <div class="tab-content">

                <div class="card mt-4">
                    <div class="card-header">
                        @if ($gajis->where('member_id', $member->id)->first())
                            @if ($gajian)
                                @if ($gajian->bulan != date('n'))
                                    <a href="{{ route('penggajian.create', $member->id) }}"
                                        class="btn btn-success text-white me-1"><i class='bx bxs-edit'></i> add
                                        penggajian</a>
                                @endif
                            @else
                                <a href="{{ route('penggajian.create', $member->id) }}"
                                    class="btn btn-success text-white me-1"><i class='bx bxs-edit'></i> add
                                    penggajian</a>
                            @endif
                        @endif
                    </div>
                    <div class="card-body">
                        {{ $gajis->links() }}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" >
                                <thead>
                                    <tr>
                                        <th>
                                            tanggal
                                        </th>
                                        <th>
                                            bulan
                                        </th>
                                        <th>
                                            tahun
                                        </th>
                                        <th>
                                            gapok
                                        </th>
                                        <th>
                                            lama kerja
                                        </th>
                                        <th>
                                            bagian
                                        </th>
                                        <th>
                                            performance
                                        </th>
                                        <th>
                                            transportasi
                                        </th>
                                        <th>
                                            komunikasi
                                        </th>
                                        <th>
                                            kehadiran
                                        </th>
                                        <th>
                                            jumlah lain
                                        </th>
                                        <th>
                                            ket lain
                                        </th>
                                        <th>
                                            jam lembur
                                        </th>
                                        <th>
                                            lembur
                                        </th>
                                        <th>
                                            kasbon
                                        </th>
                                        <th>
                                            bonus
                                        </th>
                                        <th>
                                            print
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penggajians as $item)
                                        <tr>
                                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                            <td>{{ $item->bulanAsli }}</td>
                                            <td>{{ $item->tahun }}</td>
                                            <td>{{ number_format($item->pokok) }}</td>
                                            <td>{{ number_format($item->lama_kerja) }}</td>
                                            <td>{{ number_format($item->bagian) }}</td>
                                            <td>{{ number_format($item->performance) }}</td>
                                            <td>{{ number_format($item->transportasi) }}</td>
                                            <td>{{ number_format($item->komunikasi) }}</td>
                                            <td>{{ number_format($item->kehadiran) }}</td>
                                            <td>{{ number_format($item->jumlah_lain) }}</td>
                                            <td>{{ $item->lain_lain }}</td>
                                            <td>{{ $item->jam_lembur }}</td>
                                            <td>{{ number_format($item->lembur) }}</td>
                                            <td>{{ number_format($item->kasbon) }}</td>
                                            <td>{{ number_format($item->bonus) }}</td>
                                            <td><a href="{{ route('penggajian.slip', $item->id) }}"
                                                    class="btn btn-primary btn-sm">slip gaji</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tour penggajian -->

    </div>
@endsection
