<?php

namespace App\Http\Controllers;

use App\Models\AnggaranAdministrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnggaranController extends Controller
{
    // Daftar kategori anggaran: key = nilai di DB, value = label tampilan
    protected $kategoriList = [
        'Dikelola'  => 'Anggaran Dikelola',
        'Rutin'     => 'Anggaran Rutin',
        'Investasi' => 'Anggaran Investasi',
    ];

    public function index(Request $request)
    {
        // =========================================================
        // 1. SUMBER OPSI FILTER
        //    - Tahun: "Semua Tahun" + tahun yang BENAR-BENAR ada datanya (dari input user)
        //    - Bulan: "Semua Bulan" + Januari s/d Desember (selalu lengkap 12 bulan)
        // =========================================================
        $availableYears = AnggaranAdministrasi::select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $bulanOrder = collect(\Carbon\CarbonPeriod::create('2024-01-01', '1 month', '2024-12-01'))
            ->map(fn ($m) => $m->translatedFormat('F'))
            ->values();

        // 'all' = opsi "Semua Tahun" / "Semua Bulan"
        $selectedYear = $request->input('year', 'all');
        $selectedMonth = $request->input('month', 'all');

        $kategoriOrder = array_keys($this->kategoriList);

        // =========================================================
        // 2. QUERY DASAR MENGIKUTI FILTER (dipakai Chart & ketiga Tabel)
        // =========================================================
        $baseQuery = AnggaranAdministrasi::query();
        if ($selectedYear !== 'all') {
            $baseQuery->where('tahun', $selectedYear);
        }
        if ($selectedMonth !== 'all') {
            $baseQuery->where('bulan', $selectedMonth);
        }

        // =========================================================
        // A. CHART PIE per kategori (ikut filter Tahun & Bulan)
        // =========================================================
        $chartConfig = [];
        $totalRkap = 0;
        $totalRealPlusKomit = 0;
        $totalSisa = 0;

        foreach ($this->kategoriList as $key => $label) {
            $base = (clone $baseQuery)->where('kategori', $key);

            $kRkap = (clone $base)->sum('rkap');
            $kKomitmen = (clone $base)->sum('komitmen');
            $kRealisasi = (clone $base)->sum('realisasi');
            $kRealPlusKomit = $kKomitmen + $kRealisasi;
            $kSisa = $kRkap - $kRealPlusKomit;

            $percTerpakai = $kRkap > 0 ? round($kRealPlusKomit / $kRkap * 100, 1) : 0;
            $percSisa = $kRkap > 0 ? round($kSisa / $kRkap * 100, 1) : 0;

            $chartConfig[$key . 'Chart'] = [
                'type' => 'pie',
                'data' => [$percTerpakai, $percSisa],
                'labels' => ['Penggunaan', 'Sisa'],
                'colors' => ['#0056A3', '#F7941E'],
                'hasData' => $kRkap > 0,
            ];

            $totalRkap += $kRkap;
            $totalRealPlusKomit += $kRealPlusKomit;
            $totalSisa += $kSisa;
        }

        // =========================================================
        // Ambil semua baris sesuai filter (dipakai 3 tabel di bawah)
        // =========================================================
        $allRows = (clone $baseQuery)->get();

        // Daftar Tahun yang benar-benar muncul di hasil filter, urut kronologis (lama -> baru)
        $tahunTampil = $allRows->pluck('tahun')->unique()->sort()->values();

        // =========================================================
        // B. TABEL 1: Rincian per Tahun+Bulan, dikelompokkan per kategori
        // =========================================================
        $detailTable = collect();
        foreach ($tahunTampil as $tahun) {
            foreach ($bulanOrder as $bulan) {
                $rowsBulan = $allRows->where('tahun', $tahun)->where('bulan', $bulan);
                if ($rowsBulan->isEmpty()) {
                    continue;
                }

                $perKategori = [];
                foreach ($kategoriOrder as $kat) {
                    $items = $rowsBulan->where('kategori', $kat)->sortBy('detail_anggaran')->values();
                    if ($items->isNotEmpty()) {
                        $perKategori[$kat] = $items;
                    }
                }

                if (!empty($perKategori)) {
                    $detailTable->push([
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'kategori' => $perKategori,
                    ]);
                }
            }
        }

        // =========================================================
        // C. TABEL 2: Ringkasan total gabungan (semua kategori) per Tahun+Bulan
        // =========================================================
        $summaryPerBulan = collect();
        foreach ($tahunTampil as $tahun) {
            foreach ($bulanOrder as $bulan) {
                $rowsBulan = $allRows->where('tahun', $tahun)->where('bulan', $bulan);
                if ($rowsBulan->isEmpty()) {
                    continue;
                }

                $rkap = $rowsBulan->sum('rkap');
                $komitmen = $rowsBulan->sum('komitmen');
                $realisasi = $rowsBulan->sum('realisasi');
                $realPlusKomit = $komitmen + $realisasi;
                $sisa = $rkap - $realPlusKomit;

                $summaryPerBulan->push([
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'percRk' => $rkap > 0 ? round($realPlusKomit / $rkap * 100, 1) : 0,
                    'percSisa' => $rkap > 0 ? round($sisa / $rkap * 100, 1) : 0,
                    'rkap' => $rkap,
                    'komitmen' => $komitmen,
                    'realisasi' => $realisasi,
                    'realPlusKomit' => $realPlusKomit,
                    'sisa' => $sisa,
                ]);
            }
        }

        // =========================================================
        // D. TABEL 3: Perbandingan Sisa Anggaran antar kategori per Tahun+Bulan
        // =========================================================
        $sisaPerKategori = collect();
        foreach ($tahunTampil as $tahun) {
            foreach ($bulanOrder as $bulan) {
                $rowsBulan = $allRows->where('tahun', $tahun)->where('bulan', $bulan);
                if ($rowsBulan->isEmpty()) {
                    continue;
                }

                $sisaTiapKategori = [];
                foreach ($kategoriOrder as $kat) {
                    $rowsKat = $rowsBulan->where('kategori', $kat);
                    $rkapKat = $rowsKat->sum('rkap');
                    $realPlusKomitKat = $rowsKat->sum('komitmen') + $rowsKat->sum('realisasi');
                    $sisaTiapKategori[$kat] = $rkapKat - $realPlusKomitKat;
                }

                $sisaPerKategori->push([
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'keterangan' => 'Sisa',
                    'sisa' => $sisaTiapKategori,
                ]);
            }
        }

        return view('anggaran.index', [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'availableYears' => $availableYears,
            'bulanOrder' => $bulanOrder,
            'kategoriList' => $this->kategoriList,
            'chartConfig' => $chartConfig,
            'totalRkap' => $totalRkap,
            'totalRealPlusKomit' => $totalRealPlusKomit,
            'totalSisa' => $totalSisa,
            'detailTable' => $detailTable,
            'summaryPerBulan' => $summaryPerBulan,
            'sisaPerKategori' => $sisaPerKategori,
        ]);
    }

    public function store(Request $request)
    {
        // --- VALIDASI KETAT DAN HARGA ---
        // Sesuai ketentuan, kita perlu membersihkan titik harga sebelum divalidasi sebagai integer
        $request->merge([
            'rkap' => str_replace('.', '', $request->input('rkap')),
            'komitmen' => str_replace('.', '', $request->input('komitmen')),
            'realisasi' => str_replace('.', '', $request->input('realisasi')),
        ]);

        $request->validate([
            'tahun' => 'required',
            'bulan' => 'required',
            'kategori' => 'required|in:' . implode(',', array_keys($this->kategoriList)),
            'detail_anggaran' => 'required|string|max:255',

            'rkap' => 'required|integer|min:1',
            'komitmen' => 'required|integer|min:1',
            'realisasi' => 'required|integer|min:1',

            'keterangan' => 'nullable|string',
        ], [
            '*.required' => 'Wajib diisi.',
            '*.integer' => 'Harus berupa angka integer.',
            '*.min' => 'Wajib lebih besar dari 0.',
        ]);

        AnggaranAdministrasi::create($request->all());

        return redirect()->route('anggaran.index')
            ->with('success', 'Data anggaran bulan ' . $request->bulan . ' ' . $request->tahun . ' berhasil ditambahkan.');
    }
}