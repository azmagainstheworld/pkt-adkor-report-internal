<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelaporan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PelaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Pengaturan Tanggal & Filter Default
        $tanggalToday = Carbon::now()->translatedFormat('l, d F Y');
        
        // Ambil daftar tahun unik yang datanya SUDAH ADA di database
        $tahunTersedia = Pelaporan::select(DB::raw('YEAR(tanggal) as tahun'))
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun')
                            ->toArray();

        // Jika database masih kosong, beri default tahun saat ini
        if (empty($tahunTersedia)) {
            $tahunTersedia = [Carbon::now()->year];
        }

        // Ambil Filter Tahun dan Bulan (Default: 'semua')
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua'); 

        $mapBulan = [
            'Januari' => 1, 'February' => 2, 'Februari' => 2,
            'Maret' => 3, 'April' => 4, 'Mei' => 5,
            'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        // --- QUERY TABEL 2: RINCIAN PELAPORAN ---
        $queryRincian = Pelaporan::query();

        if ($filterTahun != 'semua') {
            $queryRincian->whereYear('tanggal', $filterTahun);
        }

        if ($filterBulan != 'semua') {
            $monthNum = $mapBulan[$filterBulan] ?? null;
            if ($monthNum) {
                $queryRincian->whereMonth('tanggal', $monthNum);
            }
        }

        $dataRincian = $queryRincian->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        // --- QUERY TABEL 1: RINGKASAN AKUMULASI (Disesuaikan dengan Filter) ---
        // Alih-alih "3 Bulan Terakhir", kita akan mengelompokkan data berdasarkan Bulan & Tahun
        // sesuai dengan filter yang sedang aktif.
        $queryRingkasan = Pelaporan::query()
            ->select(
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('MONTH(tanggal) as bulan_num'),
                DB::raw('SUM(CASE WHEN tujuan = "Eksternal" THEN 1 ELSE 0 END) as eksternal'),
                DB::raw('SUM(CASE WHEN tujuan = "Internal" THEN 1 ELSE 0 END) as internal')
            );

        if ($filterTahun != 'semua') {
            $queryRingkasan->whereYear('tanggal', $filterTahun);
        }
        if ($filterBulan != 'semua') {
            $monthNum = $mapBulan[$filterBulan] ?? null;
            if ($monthNum) {
                $queryRingkasan->whereMonth('tanggal', $monthNum);
            }
        }

        // Dapatkan data yang sudah dikelompokkan dan diurutkan dari terbaru
        $ringkasanRaw = $queryRingkasan->groupBy('tahun', 'bulan_num')
                                       ->orderBy('tahun', 'desc')
                                       ->orderBy('bulan_num', 'desc')
                                       ->get();

        $daftarBulanTeks = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $dataRingkasan = [];

        foreach ($ringkasanRaw as $row) {
            $dataRingkasan[] = [
                'tahun' => $row->tahun,
                'bulan' => $daftarBulanTeks[$row->bulan_num],
                'eksternal' => $row->eksternal,
                'internal' => $row->internal,
                'total_laporan' => $row->eksternal + $row->internal,
            ];
        }

        // --- QUERY CHART (Grouped Bar Dinamis) ---
        $chartData = [];

        if ($filterTahun == 'semua') {
            // JIKA SEMUA TAHUN: Sumbu X adalah Tahun
            $tahunAsc = array_reverse($tahunTersedia);
            $chartQuery = Pelaporan::query();
            
            if ($filterBulan != 'semua') {
                $monthNum = $mapBulan[$filterBulan] ?? null;
                if ($monthNum) $chartQuery->whereMonth('tanggal', $monthNum);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($tahunAsc as $thn) {
                $dataTahunIni = $rawDataForChart->filter(function($item) use ($thn) {
                    return Carbon::parse($item->tanggal)->year == $thn;
                });
                
                $chartData[] = [
                    'label' => (string)$thn,
                    'eksternal' => $dataTahunIni->where('tujuan', 'Eksternal')->count(),
                    'internal' => $dataTahunIni->where('tujuan', 'Internal')->count()
                ];
            }
        } else {
            // JIKA TAHUN SPESIFIK: Sumbu X adalah 12 Bulan
            $chartMonthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartQuery = Pelaporan::whereYear('tanggal', $filterTahun);
            
            if ($filterBulan != 'semua') {
                $monthNum = $mapBulan[$filterBulan] ?? null;
                if ($monthNum) $chartQuery->whereMonth('tanggal', $monthNum);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($chartMonthsShort as $index => $monthShort) {
                $monthNumber = $index + 1; 
                $dataBulanIni = $rawDataForChart->filter(function($item) use ($monthNumber) {
                    return Carbon::parse($item->tanggal)->month == $monthNumber;
                });

                $chartData[] = [
                    'label' => $monthShort,
                    'eksternal' => $dataBulanIni->where('tujuan', 'Eksternal')->count(),
                    'internal' => $dataBulanIni->where('tujuan', 'Internal')->count()
                ];
            }
        }

        return view('pelaporan', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'dataRincian', 'dataRingkasan', 'chartData'
        ));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'tujuan'  => 'required|in:Eksternal,Internal',
            'nomor'   => 'required|string|max:100',
            'laporan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jenis'   => 'required|in:Bulan,Semester,Triwulan,Tahun',
        ]);

        Pelaporan::findOrFail($id)->update($request->all());

        return back()->with('success', 'Data laporan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Pelaporan::findOrFail($id)->delete();
        return back()->with('success', 'Data laporan berhasil dihapus.');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'tujuan'  => 'required|in:Eksternal,Internal',
            'nomor'   => 'required|string|max:100',
            'laporan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jenis'   => 'required|in:Bulan,Semester,Triwulan,Tahun',
        ]);

        Pelaporan::create($request->all());

        return redirect()->route('pelaporan.index', [
            'tahun' => 'semua',
            'bulan' => 'semua'
        ])->with('success', 'Data laporan berhasil ditambahkan.');
    }
}