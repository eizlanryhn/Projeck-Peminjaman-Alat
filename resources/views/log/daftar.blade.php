@extends('layouts.utama')
@section('judul', 'Log Aktivitas')
@section('konten')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Log Aktivitas</h4>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            @include('log.form-search')
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                @include('log.tabel-log')
            </div>
            <div class="p-3">
                {{ $daftarLog->links() }}
            </div>
        </div>
    </div>
@endsection
