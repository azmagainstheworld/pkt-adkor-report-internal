<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Import semua model dari menu lain yang datanya mau ditarik
use App\Models\PaTekstualData;
use App\Models\PaNonTekstualValue;
use App\Models\PaTeknikData;
use App\Models\DofData;
// use App\Models\PengirimanData; <-- Nanti tinggal di-uncomment kalau tabel pengiriman sudah dibuat

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');
        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        // =====================================================================
        // 1. RUMUS PEMETAAN DATA (MIRIP RUMUS EXCEL)
        // Di sinilah Anda mengatur dari mana data setiap kolom Rekap berasal.
        // =====================================================================
        $rumusRekap = [
            'Pusat Arsip' => function($y, $b) {
                // Menjumlahkan Tekstual + Non Tekstual
                $tek = PaTekstualData::where('tahun', $y)->when($b, fn($q) => $q->where('bulan', $b))->sum('jumlah');
                $non = PaNonTekstualValue::where('tahun', $y)->when($b, fn($q) => $q->where('bulan', $b))->sum('jumlah');
                return $tek + $non;
            },
            'Teknikal File' => function($y, $b) {
                return PaTeknikData::where('tahun', $y)->when($b, fn($q) => $q->where('bulan', $b))->sum('jumlah');
            },
            'Approval File Scan' => function($y, $b) {
                return DofData::whereHas('masterDof', fn($q) => $q->where('nama_kegiatan', 'Approval File Scan'))
                              ->where('tahun', $y)->when($b, fn($q) => $q->where('bulan', $b))->sum('jumlah');
            },
            'Approval Stempel Digital' => function($y, $b) {
                return DofData::whereHas('masterDof', fn($q) => $q->where('nama_kegiatan', 'Approval Stempel Digital'))
                              ->where('tahun', $y)->when($b, fn($q) => $q->where('bulan', $b))->sum('jumlah');
            },
            'Digital Signature' => function($y, $b) {
                return DofData::whereHas('masterDof', fn($q) => $q->where('nama_kegiatan', 'Digital Signature'))
                              ->where('tahun', $y)->when($b, fn($q) => $q->where('bulan', $b))->sum('jumlah');
            },
            // TAMBAHAN BARU: E-meterai ditarik otomatis dari menu DOF
            'E-meterai' => function($y, $b) {
                return DofData::whereHas('masterDof', fn($q) => $q->where('nama_kegiatan', 'E-meterai'))
                              ->where('tahun', $y)->when($b, fn($q) => $q->where('bulan', $b))->sum('jumlah');
            },
            // Kolom DOF mengambil 7 item spesifik sesuai rumus Excel
            'DOF' => function($y, $b) {
                return DofData::whereHas('masterDof', fn($q) => $q->whereIn('nama_kegiatan', [
                                  'Pendaftaran Akun',
                                  'Perekaman Akun',
                                  'Revisi DOF',
                                  'Pembatalan DOF',
                                  'Pembuatan Template',
                                  'Cek Error',
                                  'Problem DOF'
                              ]))
                              ->where('tahun', $y)->when($b, fn($q) => $q->where('bulan', $b))->sum('jumlah');
            },
        ];
        // =====================================================================

        // 2. MENCARI SEMUA TAHUN & BULAN YANG ADA DI DATABASE
        $semuaWaktu = collect();
        $tabelSumber = [
            PaTekstualData::select('tahun', 'bulan'), 
            PaNonTekstualValue::select('tahun', 'bulan'),
            PaTeknikData::select('tahun', 'bulan'), 
            DofData::select('tahun', 'bulan')
        ];

        foreach ($tabelSumber as $q) {
            $data = (clone $q)->when($filterTahun != 'semua', fn($q) => $q->where('tahun', $filterTahun))
                              ->when($filterBulan != 'semua', fn($q) => $q->where('bulan', $filterBulan))->get();
            foreach ($data as $d) { $semuaWaktu->push($d->tahun . '_' . $d->bulan); }
        }
        
        $waktuUnik = $semuaWaktu->unique();
        $tahunTersedia = $semuaWaktu->map(fn($item) => explode('_', $item)[0])->unique()->sortDesc()->values()->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        // 3. MENYUSUN DATA UNTUK TABEL
        $dataTable = []; 
        $totalsKolom = array_fill_keys(array_keys($rumusRekap), 0);

        foreach($waktuUnik as $ym) {
            $parts = explode('_', $ym);
            $y = $parts[0]; $b = $parts[1];

            $row = ['tahun' => $y, 'bulan' => $b, 'kolom' => []];

            // Hitung setiap kolom berdasarkan rumus di atas
            foreach ($rumusRekap as $namaKolom => $fungsiHitung) {
                $nilai = $fungsiHitung($y, $b);
                $row['kolom'][$namaKolom] = $nilai;
                $totalsKolom[$namaKolom] += $nilai;
            }
            $dataTable[] = $row;
        }

        // Sorting Bulan (Januari ke Desember)
        $urutanBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        usort($dataTable, function($a, $b) use ($urutanBulan) {
            if($a['tahun'] == $b['tahun']) return $urutanBulan[$b['bulan']] <=> $urutanBulan[$a['bulan']];
            return $b['tahun'] <=> $a['tahun'];
        });

        // 4. MENYUSUN DATA UNTUK CHART.JS
        $warnaChart = ['#0056A3', '#F7941E', '#60A5FA', '#FB923C', '#A855F7', '#22C55E', '#EF4444'];
        $chartLabels = $filterTahun == 'semua' ? array_values($tahunTersedia) : ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        sort($chartLabels);

        $chartDatasets = [];
        $indeksWarna = 0;
        
        foreach ($rumusRekap as $namaKolom => $fungsiHitung) {
            $dataArr = [];
            foreach($chartLabels as $label) {
                $y = $filterTahun == 'semua' ? $label : $filterTahun;
                $b = $filterTahun == 'semua' ? null : $label;
                $dataArr[] = $fungsiHitung($y, $b);
            }
            $chartDatasets[] = [
                'label' => $namaKolom,
                'data' => $dataArr,
                'backgroundColor' => $warnaChart[$indeksWarna % count($warnaChart)]
            ];
            $indeksWarna++;
        }

        $chartJsonData = ['labels' => $chartLabels, 'datasets' => $chartDatasets];

        // Daftar header kolom untuk di-passing ke view
        $headers = array_keys($rumusRekap);

        return view('rekap', compact('tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia', 'dataTable', 'totalsKolom', 'chartJsonData', 'headers'));
    }
}