<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Parkir;
use App\Models\ParkirHistory;
use App\Helpers\TarifHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    /**
     * Download parking history as Excel
     */
    public function downloadHistory(Request $request)
    {
        $query = Parkir::with('kendaraan', 'user')->where('status', 'keluar');

        if ($request->has('date') && $request->date) {
            $query->whereDate('waktu_keluar', $request->date);
        }

        $histories = $query->get();

        return $this->downloadAsExcel($histories, $request->date);
    }

    /**
     * Generate Excel download with formatting
     */
    private function downloadAsExcel($histories, $date = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Parkir');

        // Set headers
        $headers = ['No', 'Nomor Plat', 'Jenis Kendaraan', 'Waktu Masuk', 'Waktu Keluar', 'Tarif (Rp)', 'Petugas'];
        $sheet->fromArray($headers, NULL, 'A1');

        // Style header row
        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        for ($col = 'A'; $col <= 'G'; $col++) {
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
        }

        // Add data rows
        $row = 2;
        $total = 0;
        $no = 1;

        foreach ($histories as $parkir) {
            $tarif = $parkir->tarif;
            $total += $tarif;

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $parkir->kendaraan->nomor_plat);
            $sheet->setCellValue('C' . $row, ucfirst($parkir->kendaraan->jenis_kendaraan));
            $sheet->setCellValue('D' . $row, $parkir->waktu_masuk->format('d/m/Y H:i'));
            $sheet->setCellValue('E' . $row, $parkir->waktu_keluar ? $parkir->waktu_keluar->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('F' . $row, $tarif);
            $sheet->setCellValue('G' . $row, $parkir->user->name);

            // Format tariff column as currency
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('[$-421]#,##0');

            // Add borders to data rows
            $rowStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($rowStyle);

            $row++;
        }

        // Add total row
        $totalRow = $row;
        $sheet->setCellValue('E' . $totalRow, 'TOTAL');
        $sheet->setCellValue('F' . $totalRow, $total);

        // Style total row
        $totalStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFC000'],
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('E' . $totalRow . ':F' . $totalRow)->applyFromArray($totalStyle);
        $sheet->getStyle('F' . $totalRow)->getNumberFormat()->setFormatCode('[$-421]#,##0');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(17);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Generate file
        $fileName = 'riwayat_parkir_' . ($date ?: date('Y-m-d')) . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
