@extends('layouts.app')

@section('content')
    <div class="mb-2">
        @include('layouts.includes.messages')
    </div>
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">Whattodo</h5>
                </div>
                @php
                    $auth = auth()->user()->roles->pluck('name')->toArray();
                @endphp
                @if(in_array('super', $auth))
                    <a href="{{ route('whattodo.create') }}" class="btn btn-primary ">Add Whattodo</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>isi</th>
                            <th>action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($whattodos as $what)
                            @if ($what->nama == 'gajian' && in_array('super', $auth))
                                <tr data-entry-id="{{ $what->id }}">
                                    <td>{{ $what->isi }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('whattodo.edit', $what->id) }}"
                                                class="btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i> Edit</a>
                                            <form action="{{ route('whattodo.destroy', $what->id) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="btn btn-danger btn-sm"><i class='bx bxs-trash'></i>
                                                    delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if ($what->nama == 'tugas' && $what->member_id == null)
                                <tr data-entry-id="{{ $what->id }}">
                                    <td>{{ $what->isi }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @if (in_array('super', $auth))
                                                <a href="{{ route('whattodo.edit', $what->id) }}"
                                                    class="btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i> Edit</a>
                                                <form action="{{ route('whattodo.destroy', $what->id) }}" method="post">
                                                    {{ csrf_field() }}
                                                    {{ method_field('delete') }}
                                                    <button type="submit" onclick="return confirm('Are you sure?')"
                                                        class="btn btn-danger btn-sm"><i class='bx bxs-trash'></i>
                                                        delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if ($what->nama == 'tugas' && in_array('super', $auth) && $what->member_id != null)
                                <tr data-entry-id="{{ $what->id }}">
                                    <td>{{ $what->isi }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('whattodo.edit', $what->id) }}"
                                                class="btn btn-info btn-sm me-1"><i class='bx bxs-edit'></i> Edit</a>
                                            <form action="{{ route('whattodo.destroy', $what->id) }}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('delete') }}
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="btn btn-danger btn-sm"><i class='bx bxs-trash'></i>
                                                    delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                        @endforeach

                        @foreach ($whatMember as $what)
                            <tr data-entry-id="{{ $what->id }}">
                                <td>{{ $what->isi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
