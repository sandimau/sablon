@extends('layouts.app')

@section('title')
    Create Cuti
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">add Cuti</h5>
                </div>
                <a href="{{ route('members.show', $member->id) }}" class="btn btn-primary ">back</a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('cuti.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="member_id" value="{{ $member->id }}">
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input class="form-control date {{ $errors->has('tanggal') ? 'is-invalid' : '' }}" type="date"
                        name="tanggal" id="tanggal" value="{{ old('tanggal') }}">
                    @if ($errors->has('tanggal'))
                        <div class="invalid-feedback">
                            {{ $errors->first('tanggal') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="keterangan">keterangan</label>
                    <textarea class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" name="keterangan" id=""
                        cols="30" rows="10">{{ old('keterangan', '') }}</textarea>
                    @if ($errors->has('keterangan'))
                        <div class="invalid-feedback">
                            {{ $errors->first('keterangan') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label>cuti/ijin</label>
                    <select class="form-select {{ $errors->has('cuti') ? 'is-invalid' : '' }}" aria-label="Default select example" name="cuti" name="cuti">
                        <option>pilih cuti/ijin</option>
                        <option value="1">cuti</option>
                        <option value="0">ijin</option>
                    </select>
                    @if ($errors->has('cuti'))
                        <div class="invalid-feedback">
                            {{ $errors->first('cuti') }}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <button class="btn btn-primary mt-4" type="submit">
                        save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
