@extends('layouts.app')

@section('content')
<h1>Data Kendaraan</h1>

<a href="{{ route('kendaraan.create') }}" class="btn btn-primary mb-3">Tambah Kendaraan</a>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nomor Plat</th>
            <th>Jenis Kendaraan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kendaraans as $kendaraan)
        <tr>
            <td>{{ $kendaraan->id }}</td>
            <td>{{ $kendaraan->nomor_plat }}</td>
            <td>{{ $kendaraan->jenis_kendaraan }}</td>
            <td>
                <a href="{{ route('kendaraan.edit', $kendaraan) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('kendaraan.destroy', $kendaraan) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection