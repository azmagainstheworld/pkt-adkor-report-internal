<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// Gunakan model tunggal yang memiliki data volume dan ongkir
use App\Models\PengirimanDokumen; 
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PengirimanDokumenController extends Controller
{
    // Struktur Bulan standar untuk visualisasi urut indonesian month sorting
    protected $masterMonths = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    public function index(Request $request)
    {
        // 1. Tentukan Filter Default (Tahun Sekarang, Bulan 'semua')
        $now = Carbon::now();
        // Default tahun sekarang
        $selectedYear = $request->input('year', $now->year); 
        // Default 'semua' bulan untuk melihat tren setahun penuh seperti Looker
        $selectedMonth = $request->input('month', 'semua'); 

        // --- PERBAIKAN FILTER TAHUN DINAMIS ---
        // Ambil tahun unik yang *memang ada* di database agar dropdown filter valid
        $availableYears = PengirimanDokumen::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Jika database benar-benar kosong, gunakan tahun sekarang sebagai fallback
        if ($availableYears->isEmpty()) {
            $availableYears = collect([$now->year]); 
        }

        // ==========================================
        // 2. QUERY DASAR BERDASARKAN FILTER
        // ==========================================
        // Kita gunakan model tunggal pengiriman_dokumen yang memiliki volume dan ongkir
        $baseQuery = PengirimanDokumen::where('tahun', $selectedYear);

        if ($selectedMonth != 'semua') {
            $baseQuery->where('bulan', $selectedMonth);
        }

        // --- DATA UNTUK CHART & AKSES TERKUNCI (Keyed) ---
        // Ambil data untuk Chart, keyBy('bulan') untuk kemudahan preparasi data
        $dataForChartBuilder = clone $baseQuery;
        $allDataKeyed = $dataForChartBuilder->get()->keyBy('bulan'); 

        // --- PREPARASI DATA CHART ---
        // Labels dinamis: 12 bulan jika 'semua', atau 1 bulan terpilih
        $chartLabels = ($selectedMonth == 'semua') ? $this->masterMonths : [$selectedMonth];
        
        $chartMailroom = [];
        $chartDof = [];
        $chartDomestikVolume = [];
        $chartInternasionalVolume = [];

        foreach ($chartLabels as $bulanNama) {
            $dataRow = $allDataKeyed->get($bulanNama);
            // Gunakan nama kolom DB yang benar dari migration combined combined combined penerimaan_mailroom, registrasi_surat_masuk_dof, dll.
            $chartMailroom[] = $dataRow ? $dataRow->penerimaan_mailroom : 0;
            $chartDof[] = $dataRow ? $dataRow->registrasi_surat_masuk_dof : 0;
            $chartDomestikVolume[] = $dataRow ? $dataRow->pengiriman_dalam_negeri : 0;
            $chartInternasionalVolume[] = $dataRow ? $dataRow->pengiriman_luar_negeri : 0;
        }

        $chartVolumeConfig = [
            'labels' => $chartLabels,
            'datasets' => [
                [
                    'label' => 'Penerimaan Mailroom',
                    'backgroundColor' => '#0056A3', // custom color birul birul birul
                    'data' => $chartMailroom
                ],
                [
                    'label' => 'Registrasi Surat Masuk via DOF',
                    'backgroundColor' => '#F7941E', // custom orange color
                    'data' => $chartDof
                ],
                [
                    'label' => 'Pengiriman Dalam Negeri',
                    'backgroundColor' => '#22C55E', // green-500
                    'data' => $chartDomestikVolume
                ],
                [
                    'label' => 'Pengiriman Luar Negeri',
                    'backgroundColor' => '#F87171', // red-400
                    'data' => $chartInternasionalVolume
                ]
            ]
        ];

        // ==========================================
        // 3. QUERY UNTUK TABEL ONGKIR DENGAN PENGURUTAN (PERBAIKAN BUG BADMETHODCALL)
        // ==========================================
        // Kita clone query builder dasar untuk menerapkan pengurutan kustom indonesian indonesian month sorting FIELD indonesian bulan FIELD sorting indonesian order by field bulan indonesian indonesian
        $costQueryBuilder = clone $baseQuery;

        // --- SOLUSI ERRORorderByRaw ---
        // Metode orderByRaw dipanggil pada Query Builder, BUKAN pada Collection.
        $costQueryBuilder->orderByRaw("FIELD(bulan, '" . implode("','", $this->masterMonths) . "')");

        // Akhirnya eksekusi get() untuk mendapatkan Collection yang sudah terurut.
        $costRecords = $costQueryBuilder->get(); 

        // --- PERHITUNGAN TOTAL ONGKIR KESELURUHAN (Collection Sum) ---
        // Gunakan nama kolom DB yang benar untuk sum sum sum: ongkir_dalam_negeri
        $totalDomestikOverall = $costRecords->sum('ongkir_dalam_negeri');
        $totalInternasionalOverall = $costRecords->sum('ongkir_luar_negeri');

        return view('pengiriman-dokumen', compact(
            'selectedYear', 'selectedMonth', 'availableYears',
            'chartVolumeConfig', // Format JSON untuk Chart
            'costRecords',       // Data Tabel Ongkir (Rincian Bulanan)
            'totalDomestikOverall',
            'totalInternasionalOverall'
        ));
    }

    public function store(Request $request)
    {
        // --- PERBAIKAN VALIDASI ---
        // Sesuaikan key validasi dengan nama input di form Blade Anda
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'volume_mailroom' => 'required|integer|min:0',
            'volume_domestik' => 'required|integer|min:0',
            'volume_internasional' => 'required|integer|min:0',
            'volume_dof' => 'required|integer|min:0',
            // Pastikan input biaya dikirim sebagai integer murni dari JS
            'cost_domestik' => 'required|integer|min:0', 
            'cost_internasional' => 'required|integer|min:0',
            // Tambahkan validasi e_materai jika ada di form, jika tidak ada beri default 0 di DB
            'e_materai' => 'nullable|integer|min:0', 
        ], [
            '*.required' => 'Bidang ini wajib diisi.',
            '*.integer' => 'Bidang ini harus berupa angka.',
        ]);

        try {
            // --- PERBAIKAN PENYIMPANAN DATA (Peta Input ke Kolom DB) ---
            PengirimanDokumen::updateOrCreate(
                // 1. Kunci Pencarian (Unique Key)
                ['tahun' => $request->tahun, 'bulan' => $request->bulan], 
                
                // 2. Data yang Diupdate/Disimpan
                [
                    // 'NAMA_KOLOM_DB' => $request->NAMA_INPUT_FORM
                    'penerimaan_mailroom' => $request->volume_mailroom,
                    'registrasi_surat_masuk_dof' => $request->volume_dof,
                    'pengiriman_dalam_negeri' => $request->volume_domestik,
                    'pengiriman_luar_negeri' => $request->volume_internasional,
                    'ongkir_dalam_negeri' => $request->cost_domestik,
                    'ongkir_luar_negeri' => $request->cost_internasional,
                    'e_materai' => $request->e_materai ?? 0, // Beri default jika null
                ]
            );

            return redirect()->route('pengiriman-dokumen.index', ['year' => $request->tahun, 'month' => $request->bulan])
                ->with('success', "Data laporan Pengiriman Dokumen bulan {$request->bulan} {$request->tahun} berhasil diperbaharui.");

        } catch (\Exception $e) {
            // Log error untuk debug
            \Log::error("Error saving PengirimanDokumen: " . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan data. Silakan coba lagi nanti.');
        }
    }
}