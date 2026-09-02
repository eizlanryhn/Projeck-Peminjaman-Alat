@extends('layouts.utama')

@section('judul', 'Masuk')

@section('konten')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="card-title mb-4 text-center">Masuk ke Sistem</h4>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <x-input name="username" label="Nama Pengguna" required autofocus />
                    <x-input name="password" label="Kata Sandi" type="password" required value="" />
                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
