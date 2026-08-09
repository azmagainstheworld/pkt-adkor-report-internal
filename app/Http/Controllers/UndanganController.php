<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Undangan;

class UndanganController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil daftar tahun unik yang datanya SUDAH ADA di database
        $tahunTersedia = Undangan::select('tahun')
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun')
                            ->toArray();

        // Jika database masih kosong, beri default tahun saat ini
        if (empty($tahunTersedia)) {
            $tahunTersedia = [date('Y')];
        }

        // 2. Ambil Filter Tahun dan Bulan
        $tahunFilter = $request->input('tahun', 'semua');
        $bulanFilter = $request->input('bulan', 'semua');

        // 3. Query Data berdasarkan Filter untuk Tabel
        $query = Undangan::query();
        
        if ($tahunFilter != 'semua') {
            $query->where('tahun', $tahunFilter);
        }
        
        if ($bulanFilter != 'semua') {
            $query->where('bulan', $bulanFilter);
        }
        
        // Urutkan data terbaru di paling atas
        $tableData = $query->orderBy('tahun', 'desc')->orderBy('id', 'desc')->get();

        // 4. Hitung Grand Total untuk Tabel
        $totalIntern = $tableData->sum('undangan_intern');
        $totalEkstern = $tableData->sum('undangan_ekstern');

        // 5. Siapkan Data untuk Chart.js (DINAMIS SUMBU X)
        $chartData = [];
        
        if ($tahunFilter == 'semua') {
            // JIKA SEMUA TAHUN: Sumbu X adalah Tahun (urut dari terlama ke terbaru)
            $tahunAsc = array_reverse($tahunTersedia);
            
            $chartQuery = Undangan::query();
            if ($bulanFilter != 'semua') {
                $chartQuery->where('bulan', $bulanFilter);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($tahunAsc as $thn) {
                $dataTahunIni = $rawDataForChart->where('tahun', $thn);
                $chartData[] = [
                    'label' => (string)$thn, // Menjadi "2025", "2026"
                    'intern' => $dataTahunIni->sum('undangan_intern'),
                    'ekstern' => $dataTahunIni->sum('undangan_ekstern'),
                ];
            }
        } else {
            // JIKA TAHUN SPESIFIK: Sumbu X adalah 12 Bulan
            $daftarBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            $chartQuery = Undangan::where('tahun', $tahunFilter);
            if ($bulanFilter != 'semua') {
                $chartQuery->where('bulan', $bulanFilter);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($daftarBulan as $bulan) {
                $dataBulanIni = $rawDataForChart->where('bulan', $bulan);
                $chartData[] = [
                    'label' => substr($bulan, 0, 3), // Menjadi "Jan", "Feb"
                    'intern' => $dataBulanIni->sum('undangan_intern'),
                    'ekstern' => $dataBulanIni->sum('undangan_ekstern'),
                ];
            }
        }

        return view('undangan', compact('tableData', 'chartData', 'tahunFilter', 'bulanFilter', 'totalIntern', 'totalEkstern', 'tahunTersedia'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'periode' => 'required|date_format:Y-m',
            'jenis_undangan' => 'required|in:intern,ekstern',
            'jumlah_undangan' => 'required|integer|min:0',
        ]);

        $parts = explode('-', $request->periode);
        $tahun = $parts[0];
        $bulanNum = (int)$parts[1]; 
        
        $daftarBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $namaBulan = $daftarBulan[$bulanNum - 1];

        $undangan = Undangan::firstOrNew([
            'tahun' => $tahun,
            'bulan' => $namaBulan
        ]);

        if (!$undangan->exists) {
            $undangan->undangan_intern = 0;
            $undangan->undangan_ekstern = 0;
        }

        if ($request->jenis_undangan === 'intern') {
            $undangan->undangan_intern = $request->jumlah_undangan;
        } else {
            $undangan->undangan_ekstern = $request->jumlah_undangan;
        }

        $undangan->save();

        return redirect()->route('undangan.index', [
            'tahun' => 'semua',
            'bulan' => 'semua'
        ])->with('success', 'Data distribusi undangan berhasil disimpan.');
    }
}