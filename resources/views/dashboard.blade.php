@extends('layouts.app')

@section('content')
<h1>Dashboard</h1>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Kendaraan Masuk Hari Ini</h5>
                <h2>{{ $totalMasuk }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Kendaraan Keluar Hari Ini</h5>
                <h2>{{ $totalKeluar }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Pendapatan</h5>
                <h2>Rp {{ number_format($totalPendapatan) }}</h2>
            </div>
        </div>
    </div>
</div>
@endsection