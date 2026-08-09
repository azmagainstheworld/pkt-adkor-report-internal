<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerizinanTerbit;
use App\Models\PerizinanProsesList;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PerizinanPerkantoranController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan locale Carbon diset ke Indonesia
        Carbon::setLocale('id');
        $tanggalToday = Carbon::now()->translatedFormat('l, d F Y');
        
        $tahunTersedia = PerizinanTerbit::select(DB::raw('YEAR(tanggal_sejak) as tahun'))
                            ->distinct()
                            ->orderBy('tahun', 'desc')
                            ->pluck('tahun')
                            ->toArray();

        if (empty($tahunTersedia)) {
            $tahunTersedia = [Carbon::now()->year];
        }

        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua'); 

        // Map nama bulan Indonesia ke angka
        $mapBulan = [
            'Januari' => 1, 'Februari' => 2,
            'Maret' => 3, 'April' => 4, 'Mei' => 5,
            'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        // --- QUERY TABEL 2: RINCIAN PERIZINAN TERBIT ---
        $queryRincian = PerizinanTerbit::query();

        if ($filterTahun != 'semua') {
            $queryRincian->whereYear('tanggal_sejak', $filterTahun);
        }

        if ($filterBulan != 'semua') {
            $monthNum = $mapBulan[$filterBulan] ?? null;
            if ($monthNum) {
                $queryRincian->whereMonth('tanggal_sejak', $monthNum);
            }
        }

        $dataRincian = $queryRincian->orderBy('tanggal_sejak', 'desc')->paginate(10)->withQueryString();

        // --- QUERY TABEL 1: RINGKASAN AKUMULASI ---
        $queryRingkasan = PerizinanTerbit::query()
            ->select(
                DB::raw('YEAR(tanggal_sejak) as tahun'),
                DB::raw('MONTH(tanggal_sejak) as bulan_num'),
                DB::raw('SUM(CASE WHEN kegiatan = "Produk" THEN 1 ELSE 0 END) as produk'),
                DB::raw('SUM(CASE WHEN kegiatan = "Aset" THEN 1 ELSE 0 END) as aset'),
                DB::raw('SUM(CASE WHEN kegiatan = "Proyek" THEN 1 ELSE 0 END) as proyek'),
                DB::raw('SUM(CASE WHEN kegiatan = "Peralatan Pabrik" THEN 1 ELSE 0 END) as peralatan_pabrik'),
                DB::raw('SUM(CASE WHEN kegiatan = "Adm & Lainnya" THEN 1 ELSE 0 END) as adm')
            );

        if ($filterTahun != 'semua') {
            $queryRingkasan->whereYear('tanggal_sejak', $filterTahun);
        }
        if ($filterBulan != 'semua') {
            $monthNum = $mapBulan[$filterBulan] ?? null;
            if ($monthNum) {
                $queryRingkasan->whereMonth('tanggal_sejak', $monthNum);
            }
        }

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
                'produk' => $row->produk,
                'aset' => $row->aset,
                'proyek' => $row->proyek,
                'peralatan_pabrik' => $row->peralatan_pabrik,
                'adm' => $row->adm,
                'total_terbit' => $row->produk + $row->aset + $row->proyek + $row->peralatan_pabrik + $row->adm,
            ];
        }

        // --- QUERY CHART ---
        $chartData = [];

        if ($filterTahun == 'semua') {
            $tahunAsc = array_reverse($tahunTersedia);
            $chartQuery = PerizinanTerbit::query();
            
            if ($filterBulan != 'semua') {
                $monthNum = $mapBulan[$filterBulan] ?? null;
                if ($monthNum) $chartQuery->whereMonth('tanggal_sejak', $monthNum);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($tahunAsc as $thn) {
                $dataTahunIni = $rawDataForChart->filter(function($item) use ($thn) {
                    return Carbon::parse($item->tanggal_sejak)->year == $thn;
                });
                
                $chartData[] = [
                    'label' => (string)$thn,
                    'produk' => $dataTahunIni->where('kegiatan', 'Produk')->count(),
                    'aset' => $dataTahunIni->where('kegiatan', 'Aset')->count(),
                    'proyek' => $dataTahunIni->where('kegiatan', 'Proyek')->count(),
                    'peralatan_pabrik' => $dataTahunIni->where('kegiatan', 'Peralatan Pabrik')->count(),
                    'adm' => $dataTahunIni->where('kegiatan', 'Adm & Lainnya')->count(),
                ];
            }
        } else {
            $chartMonthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartQuery = PerizinanTerbit::whereYear('tanggal_sejak', $filterTahun);
            
            if ($filterBulan != 'semua') {
                $monthNum = $mapBulan[$filterBulan] ?? null;
                if ($monthNum) $chartQuery->whereMonth('tanggal_sejak', $monthNum);
            }
            $rawDataForChart = $chartQuery->get();

            foreach ($chartMonthsShort as $index => $monthShort) {
                $monthNumber = $index + 1; 
                $dataBulanIni = $rawDataForChart->filter(function($item) use ($monthNumber) {
                    return Carbon::parse($item->tanggal_sejak)->month == $monthNumber;
                });

                $chartData[] = [
                    'label' => $monthShort,
                    'produk' => $dataBulanIni->where('kegiatan', 'Produk')->count(),
                    'aset' => $dataBulanIni->where('kegiatan', 'Aset')->count(),
                    'proyek' => $dataBulanIni->where('kegiatan', 'Proyek')->count(),
                    'peralatan_pabrik' => $dataBulanIni->where('kegiatan', 'Peralatan Pabrik')->count(),
                    'adm' => $dataBulanIni->where('kegiatan', 'Adm & Lainnya')->count(),
                ];
            }
        }

        // --- QUERY TABEL 3: PERIZINAN PROSES (DENGAN GROUPING ROWSPAN) ---
        $queryProses = PerizinanProsesList::query();
        if ($filterTahun != 'semua') {
            $queryProses->where('tahun', $filterTahun);
        }
        
        $dataProses = $queryProses->orderBy('tahun', 'desc')
                                  ->orderBy('nama_proses', 'asc')
                                  ->orderBy('id', 'asc')
                                  ->get();

        // Grouping data berdasarkan tahun dan nama proses untuk rowspan di Blade
        $groupedProses = $dataProses->groupBy(function ($item) {
            return $item->tahun . '_' . $item->nama_proses;
        });

        // Compact diperbaiki: pastikan nama variabel sesuai dengan yang digunakan di compact()
        return view('perizinan-perkantoran', compact(
            'tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia',
            'dataRincian', 'dataRingkasan', 'chartData', 'groupedProses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perizinan'    => 'required|string|max:150',
            'kegiatan'          => 'required|string|in:Aset,Adm & Lainnya,Peralatan Pabrik,Produk,Proyek',
            'nomor'             => 'required|string|max:100',
            // Format input date (Y-m) divalidasi sebagai date
            'tanggal_sejak'     => 'required|date',
            // Pastikan format date bisa divalidasi after
            'tanggal_akhir'     => 'required|date|after:tanggal_sejak',
            'instansi_penerbit' => 'required|string|max:150',
        ]);

        $request->merge(['kategori_grup' => $request->kegiatan]);
        PerizinanTerbit::create($request->all());

        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Rincian Perizinan Terbit berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_perizinan'    => 'required|string|max:150',
            'kegiatan'          => 'required|string|in:Aset,Adm & Lainnya,Peralatan Pabrik,Produk,Proyek',
            'nomor'             => 'required|string|max:100',
            'tanggal_sejak'     => 'required|date',
            'tanggal_akhir'     => 'required|date|after:tanggal_sejak',
            'instansi_penerbit' => 'required|string|max:150',
        ]);

        $request->merge(['kategori_grup' => $request->kegiatan]);
        PerizinanTerbit::findOrFail($id)->update($request->all());

        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Terbit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        PerizinanTerbit::findOrFail($id)->delete();
        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Terbit berhasil dihapus.');
    }

    public function storeProses(Request $request)
    {
        $request->validate([
            'tahun'       => 'required|integer',
            'nama_proses' => 'required|string|max:255',
            'target'      => 'nullable|string',
            'periode'     => 'nullable|string|max:100',
        ]);

        PerizinanProsesList::create($request->all());

        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Proses berhasil ditambahkan.');
    }

    public function updateProses(Request $request, $id)
    {
        $request->validate([
            'tahun'       => 'required|integer',
            'nama_proses' => 'required|string|max:255',
            'target'      => 'nullable|string',
            'periode'     => 'nullable|string|max:100',
        ]);

        PerizinanProsesList::findOrFail($id)->update($request->all());

        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Proses berhasil diperbarui.');
    }

    public function destroyProses($id)
    {
        PerizinanProsesList::findOrFail($id)->delete();
        return redirect()->route('perizinan-perkantoran.index')
            ->with('success', 'Data Perizinan Proses berhasil dihapus.');
    }
}