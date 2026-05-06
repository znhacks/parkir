<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Parkir;
use App\Models\ParkirHistory;
use App\Helpers\TarifHelper;
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
     * Automatically sets tarif based on vehicle type.
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

        // Get tarif based on vehicle type
        $tarif = TarifHelper::getTarifByJenis($kendaraan->jenis_kendaraan);

        $parkir = Parkir::create([
            'kendaraan_id' => $kendaraan->id,
            'waktu_masuk' => now(),
            'status' => 'masuk',
            'tarif' => $tarif,
            'user_id' => Auth::id(),
        ]);

        // Log history
        ParkirHistory::create([
            'parkir_id' => $parkir->id,
            'aksi' => 'masuk',
            'data_baru' => $parkir->toArray(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('parkir.index')->with('success', 'Kendaraan masuk berhasil. Tarif: Rp ' . number_format($tarif));
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

    /**
     * Process vehicle exit and apply tarif
     */
    public function keluar($id)
    {
        $parkir = Parkir::findOrFail($id);
        
        // Calculate and apply tarif
        $tarif = $parkir->calculateTarif();

        $oldData = $parkir->toArray();

        $parkir->update([
            'waktu_keluar' => now(),
            'status' => 'keluar',
            'tarif' => $tarif,
        ]);

        // Log history
        ParkirHistory::create([
            'parkir_id' => $parkir->id,
            'aksi' => 'keluar',
            'data_lama' => $oldData,
            'data_baru' => $parkir->toArray(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('parkir.index')->with('success', 'Kendaraan keluar berhasil. Tarif: Rp ' . number_format($tarif));
    }

    /**
     * Display parking history with optional date filter
     */
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
