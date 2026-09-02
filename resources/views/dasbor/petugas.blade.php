@extends('layouts.utama')

@section('judul', 'Dasbor Petugas')

@section('konten')
<h4>Dasbor Petugas</h4>
<p class="text-muted">Selamat datang, {{ auth()->user()->nama }}.</p>
@endsection
