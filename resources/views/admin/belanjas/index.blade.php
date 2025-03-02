@extends('layouts.app')

@section('title')
    Data Belanja
@endsection

@section('content')
    <header class="header mb-4">
        <div class="container-fluid">
            <h5 class="card-title">Arsip Belanja</h5>
            @can('belanja_create')
                <a href="{{ route('belanja.create') }}" class="btn btn-primary"><i class='bx bx-plus-circle'></i> Tambah</a>
            @endcan
        </div>
    </header>
    <div class="bg-light rounded">
        <div class="mt-2">
            @include('layouts.includes.messages')
        </div>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <form action="{{ route('belanja.index') }}" method="get" class="d-flex gap-2 align-items-center">
                        <div class="d-flex gap-2 align-items-center">
                            <label for="nota" class="form-label mb-0">Konsumen</label>
                            <div id="autocomplete" class="autocomplete">
                                <input class="autocomplete-input {{ $errors->has('kontak_id') ? 'is-invalid' : '' }}"
                                    placeholder="cari kontak" aria-label="cari kontak">
                                <span id="closeBrg"></span>
                                <ul class="autocomplete-result-list"></ul>
                                <input type="hidden" id="kontakId" name="kontak_id">
                            </div>
                            <label for="nama" class="mb-2">Produk</label>
                            <div id="autocompleteProduk" class="autocomplete">
                                <input class="autocomplete-input produk {{ $errors->has('produk_id') ? 'invalid' : '' }}"
                                    placeholder="cari produk" aria-label="cari produk">
                                <span id="closeBrgProduk"></span>
                                <ul class="autocomplete-result-list"></ul>
                                <input type="hidden" id="produkId" name="produk_id">
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <label for="tanggal" class="form-label mb-0">Dari</label>
                            <input type="date" name="dari" class="form-control">
                            <label for="tanggal" class="form-label mb-0">Sampai</label>
                            <input type="date" name="sampai" class="form-control">
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                {{ $belanjas->links() }}
                <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover mt-3">
                        <thead>
                            <tr>
                                <th>tanggal</th>
                                <th>supplier</th>
                                <th>produk</th>
                                <th>nota</th>
                                <th>total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($belanjas as $belanja)
                                <tr data-entry-id="{{ $belanja->id }}">
                                    <td>{{ date('d-m-Y', strtotime($belanja->created_at)) }}</td>
                                    <td>{{ $belanja->kontak->nama }}</td>
                                    <td><a href="{{ route('belanja.detail', $belanja->id) }}">{{ $belanja->produk }}</a></td>
                                    <td>#{{ $belanja->nota }}</td>
                                    <td>{{ number_format($belanja->total, 0, ',', '.') }}</td>
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
    <script src="https://unpkg.com/@trevoreyre/autocomplete-js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@trevoreyre/autocomplete-js/dist/style.css" />
    <script>
        new Autocomplete('#autocomplete', {
            search: input => {
                const url = "{{ url('admin/supplier/api?q=') }}" + `${escape(input)}`;
                return new Promise(resolve => {
                    if (input.length < 1) {
                        return resolve([])
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            resolve(data);
                        })
                })
            },
            getResultValue: result => result.nama,
            onSubmit: result => {
                let kontak = document.getElementById('kontakId');
                kontak.value = result.id;

                let btn = document.getElementById("closeBrg");
                btn.style.display = "block";
                btn.innerHTML =
                    `<button onclick="clearData()" type="button" class="btnClose btn-warning"><i class='bx bx-x-circle' ></i></button>`;

            },
        })

        new Autocomplete('#autocompleteProduk', {
            search: input => {
                const url = "{{ url('admin/produk/api?q=') }}" + `${escape(input)}`;
                return new Promise(resolve => {
                    if (input.length < 1) {
                        return resolve([])
                    }

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            resolve(data);
                        })
                })
            },
            getResultValue: result => result.nama,
            onSubmit: result => {
                let idProduk = document.getElementById('produkId');
                idProduk.value = result.id;

                let btn = document.getElementById("closeBrgProduk");
                btn.style.display = "block";
                btn.innerHTML =
                    `<button onclick="clearProduk()" type="button" class="btnClose btn-warning"><i class='bx bx-x-circle' ></i></button>`;
            },
        })

        function clearData() {
            let btn = document.getElementById("closeBrg");
            btn.style.display = "none";
            let auto = document.querySelector(".autocomplete-input");
            auto.value = null;
            let idProduk = document.getElementById('kontakId');
            idProduk.value = null;
        }

        function clearProduk() {
            let btn = document.getElementById("closeBrgProduk");
            btn.style.display = "none";
            let auto = document.querySelector(".autocomplete-input.produk");
            auto.value = null;
            let idProduk = document.getElementById('produkId');
            idProduk.value = null;
        }
    </script>
    <style>
        #autocomplete,
        #autocompleteProduk {
            max-width: 600px;
        }

        #closeBrg,
        #closeBrgProduk {
            position: relative;
        }

        .autocomplete-input {
            width: 300px !important;
            margin-right: 10px;
        }

        #closeBrg button,
        #closeBrgProduk button {
            position: absolute;
            right: -15px;
            top: -40px;
        }

        .btnClose {
            padding: 4px 8px;
            border: 0;
            border-radius: 50px;
            background: #fdc54c;
        }

        .autocomplete-input.is-invalid,
        .autocomplete-input.invalid {
            border: solid 1px red;
        }
    </style>
@endpush
