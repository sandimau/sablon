@extends('layouts.app')

@section('title')
    Laporan Operasional
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <form action="{{ route('laporan.operasional') }}" method="get" class="d-flex gap-2 align-items-center">
                        <label for="bulan" class="form-label mb-0">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control">
                            @foreach ($bulan as $key => $value)
                                <option value="{{ $key }}" {{ $key == (request('bulan') ?? date('Y-m')) ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Kategori Utama</th>
                                <th>Kategori</th>
                                <th>Beban</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalBelanja = 0;
                                $currentKategoriUtama = null;
                                $subtotalKategoriUtama = 0;
                            @endphp

                            @foreach($data as $item)
                                @if($currentKategoriUtama != $item->kategori_utama)
                                    @if($currentKategoriUtama !== null)
                                        {{-- Tampilkan subtotal kategori utama sebelumnya --}}
                                        <tr class="table-light">
                                            <td></td>
                                            <td></td>
                                            <td class="text-end"><strong>{{ number_format($subtotalKategoriUtama, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        @php
                                            $subtotalKategoriUtama = 0;
                                        @endphp
                                    @endif

                                    {{-- Tampilkan kategori utama baru --}}
                                    <tr class="table-light">
                                        <td><strong>{{ $item->kategori_utama }}</strong></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    @php
                                        $currentKategoriUtama = $item->kategori_utama;
                                    @endphp
                                @endif

                                {{-- Tampilkan sub-kategori --}}
                                <tr>
                                    <td></td>
                                    <td>
                                        <a href="{{ url('admin/operasionaldetail') }}?bulan={{ request('bulan') ?? date('Y-m') }}&kategori={{ $item->kategori_id }}">
                                            {{ $item->kategori }}
                                        </a>
                                    </td>
                                    <td class="text-end">{{ number_format($item->total_belanja, 0, ',', '.') }}</td>
                                </tr>

                                @php
                                    $totalBelanja += $item->total_belanja;
                                    $subtotalKategoriUtama += $item->total_belanja;
                                @endphp
                            @endforeach

                            @if($currentKategoriUtama !== null)
                                {{-- Tampilkan subtotal kategori utama terakhir --}}
                                <tr class="table-light">
                                    <td></td>
                                    <td></td>
                                    <td class="text-end"><strong>{{ number_format($subtotalKategoriUtama, 0, ',', '.') }}</strong></td>
                                </tr>
                            @endif

                            <tr class="table-primary">
                                <td><strong>Total</strong></td>
                                <td></td>
                                <td class="text-end"><strong>{{ number_format($totalBelanja, 0, ',', '.') }}</strong></td>
                            </tr>
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
            $('#bulan').on('change', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
