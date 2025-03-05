@extends('layouts.app')

@section('title')
    Data Omzet
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Aset dan Omzet Produk</h4>
            <form method="get" class="d-flex align-items-center">
                <label for="year" class="me-2">Pilih Tahun:</label>
                <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                    @foreach($tahuns as $year)
                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="card">
            <canvas id="speedChart"></canvas>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var speedCanvas = document.getElementById("speedChart");

        var myChart = new Chart(speedCanvas, {
            type: 'line',
            data: {
                labels: [
                    @php
                        foreach ($years as $value) {
                            echo '"' . $value->monthname . ' ' . $value->year . '",';
                        }
                    @endphp
                ],
                datasets: [{
                    label: 'omzet', // Name the series
                    data: [
                        @php
                            foreach ($years as $value) {
                                echo $value->omzet . ',';
                            }
                        @endphp
                    ], // Specify the data values array
                    fill: false,
                    borderColor: '#2196f3', // Add custom color border (Line)
                    backgroundColor: '#2196f3', // Add custom color background (Points and Fill)
                    borderWidth: 1 // Specify bar border width
                }]
            },
            options: {
                responsive: true, // Instruct chart js to respond nicely.
                maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height
            }
        });
    </script>
@endpush
