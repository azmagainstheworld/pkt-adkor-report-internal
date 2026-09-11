<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Ketidakhadiran;
use App\Models\KetidakhadiranHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KetidakhadiranImport;
use App\Exports\KetidakhadiranExport;
use Barryvdh\DomPDF\Facade\Pdf;

class KetidakhadiranController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('en');
        $tanggalToday = Carbon::now()->format('l, d F Y'); 

        $bulanList = Ketidakhadiran::bulanList();
        $kategoriList = Ketidakhadiran::kategoriList();

        $tahunRaw = $request->input('tahun', now()->year);
        $tahun = $tahunRaw === 'semua' ? 'semua' : (int) $tahunRaw;
        $bulanNama = $request->input('bulan', 'semua');

        if (!in_array($bulanNama, $bulanList, true) && $bulanNama != 'semua') {
            $bulanNama = $bulanList[now()->month - 1];
        }

        $tahunList = Ketidakhadiran::select('tahun')->distinct()->pluck('tahun')->toArray();
        if (!in_array(now()->year, $tahunList)) {
            $tahunList[] = now()->year;
        }
        rsort($tahunList);

        $queryKaryawan = Karyawan::query()
            ->join('ketidakhadiran', function($join) use ($tahun, $bulanNama) {
                $join->on('karyawan.id', '=', 'ketidakhadiran.karyawan_id');
                if ($tahun !== 'semua') {
                    $join->where('ketidakhadiran.tahun', '=', $tahun);
                }
                if ($bulanNama != 'semua') {
                    $join->where('ketidakhadiran.bulan', '=', $bulanNama);
                }
            })
            ->select('karyawan.id as karyawan_id', 'karyawan.nama', 'karyawan.npk',
                     'ketidakhadiran.id as ketidakhadiran_id', 
                     'ketidakhadiran.keterangan', 
                     'ketidakhadiran.dinas', 'ketidakhadiran.cuti', 'ketidakhadiran.izin',
                     'ketidakhadiran.training', 'ketidakhadiran.dispensasi', 'ketidakhadiran.detasering',
                     'ketidakhadiran.bulan', 'ketidakhadiran.tahun');
            
        if ($request->filled('search')) {
            $queryKaryawan->where(function($q) use ($request) {
                $q->where('karyawan.nama', 'like', "%{$request->search}%")
                  ->orWhere('karyawan.npk', 'like', "%{$request->search}%");
            });
        }

        if ($bulanNama == 'semua') {
            $queryKaryawan->orderBy('karyawan.nama', 'asc')
                          ->orderByRaw("FIELD(ketidakhadiran.bulan, 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember')");
        } else {
            $queryKaryawan->orderBy('karyawan.nama', 'asc');
        }

        $karyawan = $queryKaryawan->paginate(10)->withQueryString();

        $rawDataQuery = Ketidakhadiran::whereHas('karyawan');
        if ($tahun !== 'semua') {
            $rawDataQuery->where('tahun', $tahun);
        }
        if ($bulanNama != 'semua') {
            $rawDataQuery->where('bulan', $bulanNama);
        }
        if ($request->filled('search')) {
            $rawDataQuery->whereHas('karyawan', function($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('npk', 'like', "%{$request->search}%");
            });
        }
        $rawDataForChart = $rawDataQuery->get();

        $getVal = function($val) {
            return (int) preg_replace('/[^0-9]/', '', $val ?? '0');
        };

        $totalPerKategori = [
            'Dinas' => $rawDataForChart->sum(fn($i) => $getVal($i->dinas)),
            'Cuti' => $rawDataForChart->sum(fn($i) => $getVal($i->cuti)),
            'Izin' => $rawDataForChart->sum(fn($i) => $getVal($i->izin)),
            'Training' => $rawDataForChart->sum(fn($i) => $getVal($i->training)),
            'Dispensasi' => $rawDataForChart->sum(fn($i) => $getVal($i->dispensasi)),
            'Detasering' => $rawDataForChart->sum(fn($i) => $getVal($i->detasering)),
        ];
        $totalHari = array_sum($totalPerKategori);

        $daftarKaryawan = Karyawan::orderBy('nama', 'asc')->get();
        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'ketidakhadiran_harian')->get();

        return view('ketidakhadiran.index', compact(
            'tanggalToday', 'tahun', 'tahunList', 'bulanNama', 'bulanList', 
            'karyawan', 'daftarKaryawan', 'kolomDinamis', 'totalHari', 'totalPerKategori'
        ));
    }

    public function harian(Request $request)
    {
        Carbon::setLocale('en');
        $tanggalToday = Carbon::now()->format('l, d F Y'); 

        $karyawan_id = $request->input('karyawan_id');
        $tahun = $request->input('tahun', date('Y'));
        $bulanNama = $request->input('bulan', date('F')); 
        
        $daftarKaryawan = Karyawan::orderBy('nama', 'asc')->get();
        $bulanList = Ketidakhadiran::bulanList();

        $riwayat = collect([]);
        $karyawanTerpilih = null;

        if ($karyawan_id) {
            $karyawanTerpilih = Karyawan::findOrFail($karyawan_id);
            $bulanAngka = str_pad(array_search($bulanNama, $bulanList) + 1, 2, '0', STR_PAD_LEFT);
            
            $riwayat = KetidakhadiranHarian::where('karyawan_id', $karyawan_id)
                        ->whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bulanAngka)
                        ->orderBy('tanggal', 'desc')
                        ->get();
        }

        $kolomDinamis = DB::table('dynamic_columns')->where('modul', 'ketidakhadiran_harian')->get();

        return view('ketidakhadiran.harian', compact(
            'tanggalToday', 'tahun', 'bulanNama', 'bulanList', 
            'karyawanTerpilih', 'riwayat', 'daftarKaryawan', 'karyawan_id', 'kolomDinamis'
        ));
    }

    public function storeBulanan(Request $request)
    {
        $kategoriList = Ketidakhadiran::kategoriList();
        
        // Buat aturan dinamis berdasarkan kategori
        $rules = [
            'karyawan_id' => 'required|exists:karyawan,id',
            'bulan_tahun' => 'required|date_format:Y-m',
            'keterangan'  => 'nullable|string',
        ];
        foreach ($kategoriList as $kategori) {
            $rules[$kategori] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $date = \Carbon\Carbon::createFromFormat('Y-m', $validated['bulan_tahun']);
        $tahun = $date->year;
        $bulan = Ketidakhadiran::bulanList()[$date->month - 1];

        $dataToUpdate = ['keterangan' => $validated['keterangan'] ?? null];
        foreach ($kategoriList as $kategori) {
            $dataToUpdate[$kategori] = $validated[$kategori] ?? '0';
        }

        Ketidakhadiran::updateOrCreate(
            [
                'karyawan_id' => $validated['karyawan_id'],
                'tahun'       => $tahun,
                'bulan'       => $bulan,
            ],
            $dataToUpdate
        );

        return redirect()
            ->route('ketidakhadiran.index', ['tahun' => $tahun, 'bulan' => $bulan])
            ->with('success', 'Data ketidakhadiran bulanan berhasil disimpan.');
    }

    public function destroyBulanan(Ketidakhadiran $ketidakhadiran)
    {
        $tahun = $ketidakhadiran->tahun;
        $bulanNama = $ketidakhadiran->bulan;
        $ketidakhadiran->delete();

        return redirect()
            ->route('ketidakhadiran.index', ['tahun' => $tahun, 'bulan' => $bulanNama])
            ->with('success', 'Data ketidakhadiran bulanan berhasil dihapus.');
    }

    public function storeHarian(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal'     => 'required|date',
            'jenis'       => 'required|in:' . implode(',', Ketidakhadiran::kategoriList()),
            'keterangan'  => 'nullable|string',
            'data_tambahan' => 'nullable|array'
        ]);

        $tanggal = Carbon::parse($validated['tanggal']);
        $tahun = $tanggal->year;
        $bulanNama = Ketidakhadiran::bulanList()[$tanggal->month - 1];

        $existing = KetidakhadiranHarian::where('karyawan_id', $validated['karyawan_id'])
            ->whereDate('tanggal', $tanggal->toDateString())
            ->first();

        $dataTambahan = $request->input('data_tambahan', []);

        if ($existing) {
            if ($existing->jenis !== $validated['jenis']) {
                $this->adjustBulanan($validated['karyawan_id'], $tahun, $bulanNama, $existing->jenis, -1);
                $this->adjustBulanan($validated['karyawan_id'], $tahun, $bulanNama, $validated['jenis'], 1);
            }
            $existing->update([
                'jenis' => $validated['jenis'],
                'keterangan' => $validated['keterangan'] ?? null,
                'data_tambahan' => json_encode($dataTambahan),
            ]);
        } else {
            KetidakhadiranHarian::create([
                'karyawan_id' => $validated['karyawan_id'],
                'tanggal' => $tanggal->toDateString(),
                'jenis' => $validated['jenis'],
                'keterangan' => $validated['keterangan'] ?? null,
                'data_tambahan' => json_encode($dataTambahan),
            ]);
            $this->adjustBulanan($validated['karyawan_id'], $tahun, $bulanNama, $validated['jenis'], 1);
        }

        return redirect()
            ->route('ketidakhadiran.index', ['tahun' => $tahun, 'bulan' => $bulanNama])
            ->with('success', 'Data ketidakhadiran harian berhasil disimpan, rekap bulanan otomatis bertambah.');
    }

    public function destroyHarian(KetidakhadiranHarian $harian)
    {
        $tahun = $harian->tanggal->year;
        $bulanNama = Ketidakhadiran::bulanList()[$harian->tanggal->month - 1];
        $karyawanId = $harian->karyawan_id;

        $this->adjustBulanan($karyawanId, $tahun, $bulanNama, $harian->jenis, -1);
        $harian->delete();

        return redirect()
            ->route('ketidakhadiran.harian', ['karyawan_id' => $karyawanId, 'tahun' => $tahun, 'bulan' => $bulanNama])
            ->with('success', 'Data harian dihapus, rekap bulanan otomatis dikurangi.');
    }

    protected function adjustBulanan(int $karyawanId, int $tahun, string $bulanNama, string $kategori, int $delta): void
    {
        $row = Ketidakhadiran::firstOrCreate(
            ['karyawan_id' => $karyawanId, 'tahun' => $tahun, 'bulan' => $bulanNama],
            array_fill_keys(Ketidakhadiran::kategoriList(), '0')
        );

        $val = (int) $row->{$kategori};
        $row->{$kategori} = max(0, $val + $delta);
        $row->save();
    }

    public function storeKolomDinamis(Request $request)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        $request->validate([
            'modul' => 'required|string',
            'nama_kolom' => 'required|string|max:255',
            'tipe_input' => 'required|string|in:text,number,date,dropdown',
            'pilihan_dropdown' => 'nullable|string',
        ]);

        $isDuplicate = DB::table('dynamic_columns')
            ->where('modul', $request->modul)
            ->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim($request->nama_kolom))])
            ->exists();

        if ($isDuplicate) {
            return back()->with('error_modal', 'Kolom dengan nama "' . $request->nama_kolom . '" sudah ada. Silakan gunakan nama lain!');
        }

        $pilihanDropdown = null;
        if ($request->tipe_input === 'dropdown' && $request->pilihan_dropdown) {
            $arrayPilihan = array_map('trim', explode(',', $request->pilihan_dropdown));
            $pilihanDropdown = json_encode($arrayPilihan);
        }

        DB::table('dynamic_columns')->insert([
            'modul'            => $request->modul,
            'nama_kolom'       => trim($request->nama_kolom),
            'tipe_input'       => $request->tipe_input,
            'pilihan_dropdown' => $pilihanDropdown,
            'created_at'       => Carbon::now(),
            'updated_at'       => Carbon::now(),
        ]);

        return back()->with('success', 'Kolom dinamis baru berhasil ditambahkan.');
    }

    public function destroyKolomDinamis($id)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak.');
        DB::table('dynamic_columns')->where('id', $id)->delete();
        return back()->with('success', 'Kolom dinamis berhasil dihapus.');
    }

    public function import(Request $request)
    {
        set_time_limit(0);
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:51200']);

        try {
            Excel::import(new KetidakhadiranImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Ketidakhadiran berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', Ketidakhadiran::bulanList()[now()->month - 1]);
        return Excel::download(new KetidakhadiranExport(false, $tahun, $bulan), 'Data_Ketidakhadiran_'.$bulan.'_'.$tahun.'.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new KetidakhadiranExport(true), 'Template_Import_Ketidakhadiran.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', Ketidakhadiran::bulanList()[now()->month - 1]);

        $karyawan = Karyawan::leftJoin('ketidakhadiran', function ($join) use ($tahun, $bulan) {
                $join->on('ketidakhadiran.karyawan_id', '=', 'karyawan.id')
                    ->where('ketidakhadiran.tahun', $tahun)
                    ->where('ketidakhadiran.bulan', $bulan);
            })
            ->select(['karyawan.nama', 'karyawan.npk', 'ketidakhadiran.*'])
            ->orderBy('karyawan.nama', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.ketidakhadiran', compact('karyawan', 'tahun', 'bulan'))->setPaper('a4', 'landscape');
        return $pdf->download('Data_Ketidakhadiran_'.$bulan.'_'.$tahun.'.pdf');
    }

    public function destroyBulananBulk(\Illuminate\Http\Request $request)
    {
        $ids = $request->ids;
        if ($ids && is_array($ids)) {
            \App\Models\Ketidakhadiran::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Berhasil menghapus data secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }

    public function destroyHarianBulk(\Illuminate\Http\Request $request)
    {
        $ids = $request->ids;
        if ($ids && is_array($ids)) {
            \App\Models\KetidakhadiranHarian::whereIn('id', $ids)->delete();
            return redirect()->back()->with('success', 'Berhasil menghapus data secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }

    /**
     * Mengambil data Ketidakhadiran untuk laporan PDF bulanan.
     * Single source of truth: identik dengan dashboard untuk filter Tahun + Bulan.
     * Prioritas: Data Bulanan (ketidakhadiran). Jika kosong, tidak fallback ke harian 
     * (karena ketidakhadiran bulanan sudah terakumulasi dari harian).
     */
    public static function getReportData($tahun, $bulan)
    {
        $mapBulanNum = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];
        $bulanNum = $mapBulanNum[$bulan] ?? null;

        $ketidakhadiran = \App\Models\Ketidakhadiran::join('karyawan', 'ketidakhadiran.karyawan_id', '=', 'karyawan.id')
            ->where('ketidakhadiran.tahun', $tahun)
            ->where(function($q) use ($bulan, $bulanNum) {
                $q->whereRaw('LOWER(TRIM(ketidakhadiran.bulan)) = ?', [strtolower(trim($bulan))]);
                if ($bulanNum) {
                    $q->orWhereRaw('CAST(ketidakhadiran.bulan AS UNSIGNED) = ?', [$bulanNum]);
                }
            })
            ->select(
                'karyawan.nama', 'karyawan.npk',
                'ketidakhadiran.*'
            )
            ->orderBy('karyawan.nama', 'asc')
            ->get();

        $getVal = function($val) {
            return (int) preg_replace('/[^0-9]/', '', $val ?? '0');
        };

        $totalPerKategori = [
            'Dinas'      => $ketidakhadiran->sum(fn($i) => $getVal($i->dinas)),
            'Cuti'       => $ketidakhadiran->sum(fn($i) => $getVal($i->cuti)),
            'Izin'       => $ketidakhadiran->sum(fn($i) => $getVal($i->izin)),
            'Training'   => $ketidakhadiran->sum(fn($i) => $getVal($i->training)),
            'Dispensasi' => $ketidakhadiran->sum(fn($i) => $getVal($i->dispensasi)),
            'Detasering' => $ketidakhadiran->sum(fn($i) => $getVal($i->detasering)),
        ];

        \Log::info('[PDF Section] Ketidakhadiran', [
            'bulan' => $bulan, 'tahun' => $tahun,
            'count' => $ketidakhadiran->count()
        ]);

        return [
            'ketidakhadiran'   => $ketidakhadiran,
            'totalPerKategori' => $totalPerKategori,
        ];
    }
}