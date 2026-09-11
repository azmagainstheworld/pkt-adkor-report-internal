<?php

namespace App\Http\Controllers;

use App\Models\AnggaranAdministrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AnggaranImport;
use App\Exports\AnggaranExport;
use Illuminate\Pagination\LengthAwarePaginator;

class AnggaranController extends Controller
{
    // Daftar kategori anggaran: key = nilai di DB, value = label tampilan
    protected $kategoriList = [
        'Dikelola'  => 'Anggaran Dikelola',
        'Rutin'     => 'Anggaran Rutin',
        'Investasi' => 'Anggaran Investasi',
    ];

    // Urutan default untuk detail anggaran (sesuai template asli)
    protected $detailOrder = [
        'Pemeliharaan - Peralatan Kantor' => 1,
        'Cetak dan Fotocopy' => 2,
        'Pos Materai dan Pengiriman Dok.' => 3,
        'Iuran Keanggotaan' => 4,
        'Inspeksi dan Perijinan' => 5,
        'Sewa - Peralatan Pabrik & Kantor' => 6,
        'Jasa - Konsultan' => 7,
        'Rekreasi dan Olahraga' => 8,
        'Peralatan Kantor' => 9,
        'Biaya Makan Minum' => 10,
        'Perjalanan Dinas Dalam Negeri' => 11,
        'Perlengkapan & Peralatan (Alat-alat Kantor)' => 12,
        'Perlengkapan & Peralatan (Furniture Kantor)' => 13,
        'Aset Ttp dlm Proses Konstruksi-Bangunan&Prasarana (HGB)' => 14,
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
        $selectedYear = $request->input('year', $request->input('tahun', 'all'));
        $selectedMonth = $request->input('month', $request->input('bulan', 'all'));

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
                // Filter bulan case-insensitive
                $rowsBulan = $allRows->filter(function($item) use ($tahun, $bulan) {
                    return $item->tahun == $tahun && strtolower($item->bulan) === strtolower($bulan);
                });
                if ($rowsBulan->isEmpty()) {
                    continue;
                }

                $perKategori = [];
                $totRkap = 0; $totKomitmen = 0; $totRealisasi = 0;

                foreach ($kategoriOrder as $kat) {
                    $items = $rowsBulan->where('kategori', $kat)->sortBy(function($item) {
                        return $this->detailOrder[$item->detail_anggaran] ?? 999;
                    })->values();
                    if ($items->isNotEmpty()) {
                        $perKategori[$kat] = $items;
                        $totRkap += $items->sum('rkap');
                        $totKomitmen += $items->sum('komitmen');
                        $totRealisasi += $items->sum('realisasi');
                    }
                }

                if (!empty($perKategori)) {
                    $detailTable->push([
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'kategori' => $perKategori,
                        'total_rkap' => $totRkap,
                        'total_komitmen' => $totKomitmen,
                        'total_realisasi' => $totRealisasi,
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
                $rowsBulan = $allRows->filter(function($item) use ($tahun, $bulan) {
                    return $item->tahun == $tahun && strtolower($item->bulan) === strtolower($bulan);
                });
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
                $rowsBulan = $allRows->filter(function($item) use ($tahun, $bulan) {
                    return $item->tahun == $tahun && strtolower($item->bulan) === strtolower($bulan);
                });
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

        $perPage = 10;
        $page = $request->input('page', 1);

        $detailTablePaginated = new LengthAwarePaginator(
            $detailTable->forPage($page, $perPage),
            $detailTable->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $summaryPerBulanPaginated = new LengthAwarePaginator(
            $summaryPerBulan->forPage($page, $perPage),
            $summaryPerBulan->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $sisaPerKategoriPaginated = new LengthAwarePaginator(
            $sisaPerKategori->forPage($page, $perPage),
            $sisaPerKategori->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
            'detailTable' => $detailTablePaginated,
            'summaryPerBulan' => $summaryPerBulanPaginated,
            'sisaPerKategori' => $sisaPerKategoriPaginated,
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

            'rkap' => 'required|integer|min:0',
            'komitmen' => 'required|integer|min:0',
            'realisasi' => 'required|integer|min:0',

            'keterangan' => 'nullable|string',
        ], [
            '*.required' => 'Wajib diisi.',
            '*.integer' => 'Harus berupa angka integer.',
            '*.min' => 'Wajib lebih besar atau sama dengan 0.',
        ]);

        $data = $request->all();
        $data['data_tambahan'] = json_encode($request->input('data_tambahan', []));

        AnggaranAdministrasi::create($data);

        return redirect()->route('anggaran.index', ['year' => $request->tahun, 'month' => $request->bulan])
            ->with('success', 'Data anggaran bulan ' . $request->bulan . ' ' . $request->tahun . ' berhasil ditambahkan.');
    }

    public function update(Request $request, AnggaranAdministrasi $anggaran)
    {
        $request->merge([
            'rkap' => str_replace('.', '', $request->input('rkap')),
            'komitmen' => str_replace('.', '', $request->input('komitmen')),
            'realisasi' => str_replace('.', '', $request->input('realisasi')),
        ]);

        $validated = $request->validate([
            'tahun' => 'required',
            'bulan' => 'required',
            'kategori' => 'required|in:' . implode(',', array_keys($this->kategoriList)),
            'detail_anggaran' => 'required|string|max:255',
            'rkap' => 'required|integer|min:0',
            'komitmen' => 'required|integer|min:0',
            'realisasi' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ], [
            '*.required' => 'Wajib diisi.',
            '*.integer' => 'Harus berupa angka integer.',
            '*.min' => 'Wajib lebih besar atau sama dengan 0.',
        ]);

        $dataTambahan = $request->input('data_tambahan', []);
        $validated['data_tambahan'] = json_encode($dataTambahan);

        $anggaran->update($validated);

        return redirect()->route('anggaran.index', ['year' => $validated['tahun'], 'month' => $validated['bulan']])
                         ->with('success', 'Data anggaran berhasil diperbarui.');
    }

    public function destroy(AnggaranAdministrasi $anggaran)
    {
        $tahun = $anggaran->tahun;
        $bulan = $anggaran->bulan;
        $anggaran->delete();

        return redirect()->route('anggaran.index', ['year' => $tahun, 'month' => $bulan])
                         ->with('success', 'Data anggaran berhasil dihapus.');
    }

    public function import(Request $request)
    {
        set_time_limit(0);
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:51200']);

        try {
            Excel::import(new AnggaranImport, $request->file('file'));
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Data Anggaran berhasil di-import!']);
            }
            return redirect()->back()->with('success', 'Data Anggaran berhasil di-import!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', 'semua');
        return Excel::download(new AnggaranExport($tahun, $bulan), 'Laporan_Anggaran_' . $tahun . '_' . $bulan . '.xlsx');
    }

    public function downloadTemplate()
    {
        $filePath = public_path('template/Template_anggaran.xlsx');
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }
        return response()->download($filePath);
    }

    public function destroyBulk(\Illuminate\Http\Request $request)
    {
        $ids = $request->ids;
        \Illuminate\Support\Facades\Log::info("Bulk delete IDs received: " . json_encode($ids));
        
        if ($ids && is_array($ids)) {
            $deleted = \App\Models\AnggaranAdministrasi::whereIn('id', $ids)->delete();
            \Illuminate\Support\Facades\Log::info("Deleted count: " . $deleted);
            return redirect()->back()->with('success', 'Berhasil menghapus data secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }

    /**
     * Mengambil data Anggaran untuk laporan PDF bulanan.
     * Single source of truth: identik dengan dashboard (filter Tahun + Bulan spesifik).
     */
    public static function getReportData($tahun, $bulan)
    {
        $mapBulanNum = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];
        $bulanNum = $mapBulanNum[$bulan] ?? null;

        $anggaran = \App\Models\AnggaranAdministrasi::where('tahun', $tahun)
            ->where(function($q) use ($bulan, $bulanNum) {
                $q->whereRaw('LOWER(TRIM(bulan)) = ?', [strtolower(trim($bulan))]);
                if ($bulanNum) {
                    $q->orWhereRaw('CAST(bulan AS UNSIGNED) = ?', [$bulanNum]);
                }
            })
            ->get();

        $kategoriList = [
            'Dikelola'  => 'Anggaran Dikelola',
            'Rutin'     => 'Anggaran Rutin',
            'Investasi' => 'Anggaran Investasi',
        ];

        $detailOrder = [
            'Pemeliharaan - Peralatan Kantor' => 1,
            'Cetak dan Fotocopy' => 2,
            'Pos Materai dan Pengiriman Dok.' => 3,
            'Iuran Keanggotaan' => 4,
            'Inspeksi dan Perijinan' => 5,
            'Sewa - Peralatan Pabrik & Kantor' => 6,
            'Jasa - Konsultan' => 7,
            'Rekreasi dan Olahraga' => 8,
            'Peralatan Kantor' => 9,
            'Biaya Makan Minum' => 10,
            'Perjalanan Dinas Dalam Negeri' => 11,
            'Perlengkapan & Peralatan (Alat-alat Kantor)' => 12,
            'Perlengkapan & Peralatan (Furniture Kantor)' => 13,
            'Aset Ttp dlm Proses Konstruksi-Bangunan&Prasarana (HGB)' => 14,
        ];

        // Grouping per kategori untuk tampilan tabel
        $perKategori = [];
        foreach (array_keys($kategoriList) as $kat) {
            $items = $anggaran->where('kategori', $kat)->sortBy(function($item) use ($detailOrder) {
                return $detailOrder[$item->detail_anggaran] ?? 999;
            })->values();
            if ($items->isNotEmpty()) {
                $perKategori[$kat] = $items;
            }
        }

        $totalRkap         = $anggaran->sum('rkap');
        $totalRealisasi    = $anggaran->sum('realisasi');
        $totalKomitmen     = $anggaran->sum('komitmen');
        $totalRealPlusKomit = $totalRealisasi + $totalKomitmen;
        $totalSisa         = $totalRkap - $totalRealPlusKomit;

        // Persentase gabungan (untuk Tabel 2: Ringkasan Realisasi & Komitmen per Bulan)
        $percRealisasiKomitmen = $totalRkap > 0 ? round($totalRealPlusKomit / $totalRkap * 100, 1) : 0;
        $percSisaAnggaran = $totalRkap > 0 ? round($totalSisa / $totalRkap * 100, 1) : 0;

        // Sisa anggaran per kategori (untuk Tabel 3: Perbandingan Sisa Anggaran Antar Kategori)
        $sisaPerKategori = [];
        foreach (array_keys($kategoriList) as $kat) {
            $rowsKat = $anggaran->where('kategori', $kat);
            $rkapKat = $rowsKat->sum('rkap');
            $realPlusKomitKat = $rowsKat->sum('komitmen') + $rowsKat->sum('realisasi');
            $sisaPerKategori[$kat] = $rkapKat - $realPlusKomitKat;
        }

        \Log::info('[PDF Section] Anggaran', [
            'bulan' => $bulan, 'tahun' => $tahun,
            'count' => $anggaran->count()
        ]);

        return [
            'anggaran'              => $anggaran,
            'perKategori'           => $perKategori,
            'kategoriList'          => $kategoriList,
            'totalRkap'             => $totalRkap,
            'totalRealisasi'        => $totalRealisasi,
            'totalKomitmen'         => $totalKomitmen,
            'totalRealPlusKomit'    => $totalRealPlusKomit,
            'totalSisa'             => $totalSisa,
            'percRealisasiKomitmen' => $percRealisasiKomitmen,
            'percSisaAnggaran'      => $percSisaAnggaran,
            'sisaPerKategori'       => $sisaPerKategori,
        ];
    }
}