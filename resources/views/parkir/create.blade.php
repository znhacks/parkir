@extends('layouts.app')

@section('content')
<h1>Input Kendaraan Masuk</h1>

<form action="{{ route('parkir.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="nomor_plat" class="form-label">Nomor Plat</label>
        <input type="text" class="form-control" id="nomor_plat" name="nomor_plat" required>
    </div>
    <div class="mb-3">
        <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label>
        <select class="form-control" id="jenis_kendaraan" name="jenis_kendaraan" required>
            <option value="motor">Motor</option>
            <option value="mobil">Mobil</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Masuk</button>
</form>
@endsection