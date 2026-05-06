<?php

namespace App\Http\Controllers;

use App\Models\Parkir;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalMasuk = Parkir::whereDate('waktu_masuk', $today)->count();
        $totalKeluar = Parkir::whereDate('waktu_keluar', $today)->where('status', 'keluar')->count();
        $totalPendapatan = Parkir::whereDate('waktu_keluar', $today)->where('status', 'keluar')->sum('tarif');

        return view('dashboard', compact('totalMasuk', 'totalKeluar', 'totalPendapatan'));
    }
}
