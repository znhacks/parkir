@extends('layouts.app')

@section('content')
<h1>Edit Kendaraan</h1>

<form action="{{ route('kendaraan.update', $kendaraan) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="nomor_plat" class="form-label">Nomor Plat</label>
        <input type="text" class="form-control" id="nomor_plat" name="nomor_plat" value="{{ $kendaraan->nomor_plat }}" required>
    </div>
    <div class="mb-3">
        <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
        <select class="form-control" id="jenis_kendaraan" name="jenis_kendaraan" required>
            <option value="motor" {{ $kendaraan->jenis_kendaraan == 'motor' ? 'selected' : '' }}>Motor</option>
            <option value="mobil" {{ $kendaraan->jenis_kendaraan == 'mobil' ? 'selected' : '' }}>Mobil</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endsection