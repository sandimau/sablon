@extends('layouts.app')

@section('title')
    Data Member
@endsection

@section('content')
    <div class="bg-light rounded">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Members</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Manage your members here.</h6>
                    </div>
                    @can('member_create')
                        <a href="{{ route('members.create') }}" class="btn btn-primary"><i class='bx bx-plus-circle'></i> Add</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    @include('layouts.includes.messages')
                </div>
                <div class="table-responsive">
                    <table class=" table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>nama lengkap</th>
                                <th>cuti</th>
                                <th>ijin</th>
                                <th>kasbon</th>
                                <th>lembur</th>
                                <th>tunjangan</th>
                                <th>umur</th>
                                <th>lama kerja</th>
                                <th>tanggal gajian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $member)
                                <tr data-entry-id="{{ $member->id }}">
                                    <td>
                                        <a href="{{ route('members.show', $member->id) }}">{{ $member->nama_lengkap ?? '' }}</a>
                                    </td>
                                    <td>{{ $member->countCuti }}</td>
                                    <td>{{ $member->countIjin }}</td>
                                    <td>{{ number_format($member->countKasbon) }}</td>
                                    <td>{{ $member->countLembur }}</td>
                                    <td>{{ number_format($member->countTunjangan) }}</td>
                                    <td>{{ $member->umur ?? '' }}</td>
                                    <td>{{ $member->lamaKerja ?? '' }}</td>
                                    <td>{{ $member->tgl_gajian}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
