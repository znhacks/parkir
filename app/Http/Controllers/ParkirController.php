<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Parkir;
use App\Models\ParkirHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ParkirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $parkirs = Parkir::with('kendaraan', 'user')->where('status', 'masuk')->get();
        return view('parkir.index', compact('parkirs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('parkir.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_plat' => 'required',
            'jenis_kendaraan' => 'required|in:motor,mobil',
        ]);

        // Check if vehicle exists, if not create
        $kendaraan = Kendaraan::firstOrCreate(
            ['nomor_plat' => $request->nomor_plat],
            ['jenis_kendaraan' => $request->jenis_kendaraan]
        );

        // Check if already parked
        $existing = Parkir::where('kendaraan_id', $kendaraan->id)->where('status', 'masuk')->first();
        if ($existing) {
            return back()->withErrors(['nomor_plat' => 'Kendaraan sudah parkir']);
        }

        $parkir = Parkir::create([
            'kendaraan_id' => $kendaraan->id,
            'waktu_masuk' => now(),
            'status' => 'masuk',
            'user_id' => Auth::id(),
        ]);

        // Log history
        ParkirHistory::create([
            'parkir_id' => $parkir->id,
            'aksi' => 'masuk',
            'data_baru' => $parkir->toArray(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('parkir.index')->with('success', 'Kendaraan masuk berhasil');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function keluar($id)
    {
        $parkir = Parkir::findOrFail($id);
        $parkir->update([
            'waktu_keluar' => now(),
            'status' => 'keluar',
            'tarif' => $parkir->calculateTarif(),
        ]);

        // Log history
        ParkirHistory::create([
            'parkir_id' => $parkir->id,
            'aksi' => 'keluar',
            'data_lama' => ['status' => 'masuk'],
            'data_baru' => $parkir->toArray(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('parkir.index')->with('success', 'Kendaraan keluar berhasil');
    }

    public function history(Request $request)
    {
        $query = Parkir::with('kendaraan', 'user')->where('status', 'keluar');

        if ($request->has('date') && $request->date) {
            $query->whereDate('waktu_keluar', $request->date);
        }

        $histories = $query->get();
        return view('parkir.history', compact('histories'));
    }
}
