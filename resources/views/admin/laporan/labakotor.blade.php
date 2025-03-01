@extends('layouts.app')

@section('title')
    Laba Kotor
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <form action="{{ route('laporan.labakotor') }}" method="get" class="d-flex gap-2 align-items-center">
                        <label for="bulan" class="form-label mb-0">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control">
                            @foreach ($bulan as $key => $value)
                                <option value="{{ $key }}" {{ $key == $selected_month ? 'selected' : '' }}>{{ $value }}</option>
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
                                @if($view_type == 'kategori')
                                    <th>Kategori</th>
                                @else
                                    <th>Kategori</th>
                                    <th>Produk</th>
                                @endif
                                <th>Omzet</th>
                                <th>HPP</th>
                                <th>Opname</th>
                                <th>Laba Kotor</th>
                                <th>Persen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalOmzet = 0;
                                $totalHpp = 0;
                                $totalOpname = 0;
                                $totalLabaKotor = 0;
                            @endphp

                            @foreach($data as $item)
                                <tr>
                                    @if($view_type == 'kategori')
                                        <td>
                                            <a href="{{ route('laporan.labakotordetail', ['bulan' => $selected_month, 'kategori' => $item->kategori_id]) }}">
                                                {{ $item->kategori }}
                                            </a>
                                        </td>
                                    @else
                                        <td>{{ $item->kategori }}</td>
                                        <td>{{ $item->produk }}</td>
                                    @endif
                                    <td>{{ number_format($item->omzet, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->hpp, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->opname, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->laba_kotor, 0, ',', '.') }}</td>
                                    <td>{{ number_format($item->persen, 0, ',', '.') }}%</td>
                                </tr>

                                @php
                                    $totalOmzet += $item->omzet;
                                    $totalHpp += $item->hpp;
                                    $totalOpname += $item->opname;
                                    $totalLabaKotor += $item->laba_kotor;
                                @endphp
                            @endforeach

                            <tr class="table-primary">
                                <td colspan="{{ $view_type == 'kategori' ? '1' : '2' }}"><strong>Total Keseluruhan</strong></td>
                                <td><strong>{{ number_format($totalOmzet, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ number_format($totalHpp, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ number_format($totalOpname, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ number_format($totalLabaKotor, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ $totalOmzet > 0 ? number_format(($totalLabaKotor/$totalOmzet)*100, 0, ',', '.') : 0 }}%</strong></td>
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
            $('#bulan, #view_type').on('change', function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
