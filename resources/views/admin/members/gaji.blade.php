@extends('layouts.app')

@section('title')
    Data Member Gaji
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Gaji</h5>
                    </div>
                </div>
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
                            @foreach ($gajis as $item)
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
@endsection
