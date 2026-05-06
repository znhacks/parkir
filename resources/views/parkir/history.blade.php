@extends('layouts.app')

@section('content')
<h1>Riwayat Parkir</h1>

<form action="{{ route('parkir.history') }}" method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-4">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nomor Plat</th>
            <th>Jenis</th>
            <th>Waktu Masuk</th>
            <th>Waktu Keluar</th>
            <th>Tarif</th>
            <th>Petugas</th>
        </tr>
    </thead>
    <tbody>
        @forelse($histories as $parkir)
        <tr>
            <td>{{ $parkir->id }}</td>
            <td>{{ $parkir->kendaraan->nomor_plat }}</td>
            <td>{{ ucfirst($parkir->kendaraan->jenis_kendaraan) }}</td>
            <td>{{ $parkir->waktu_masuk->format('d/m/Y H:i') }}</td>
            <td>{{ $parkir->waktu_keluar ? $parkir->waktu_keluar->format('d/m/Y H:i') : '-' }}</td>
            <td><strong>Rp {{ number_format($parkir->tarif) }}</strong></td>
            <td>{{ $parkir->user->name }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada riwayat parkir</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection