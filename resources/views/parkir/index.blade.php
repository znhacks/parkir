@extends('layouts.app')

@section('content')
<h1>Kendaraan Parkir</h1>

<a href="{{ route('parkir.create') }}" class="btn btn-primary mb-3">Input Kendaraan Masuk</a>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nomor Plat</th>
            <th>Jenis</th>
            <th>Waktu Masuk</th>
            <th>Tarif</th>
            <th>Petugas</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parkirs as $parkir)
        <tr>
            <td>{{ $parkir->id }}</td>
            <td>{{ $parkir->kendaraan->nomor_plat }}</td>
            <td>{{ ucfirst($parkir->kendaraan->jenis_kendaraan) }}</td>
            <td>{{ $parkir->waktu_masuk->format('d/m/Y H:i') }}</td>
            <td><strong>Rp {{ number_format($parkir->tarif) }}</strong></td>
            <td>{{ $parkir->user->name }}</td>
            <td>
                <form action="{{ route('parkir.keluar', $parkir) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">Keluar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection