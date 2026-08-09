<?php

namespace App\Http\Controllers;

use App\Models\BarSkMemoRapat;
use App\Models\DofData;
use App\Models\MemoTerbit;
use App\Models\Pelaporan;
use App\Models\PengirimanDokumen;
use App\Models\PerizinanTerbit;
use App\Models\RekapData;
use App\Models\PaTeknikData;
use App\Models\SkdTerbit;
use App\Models\Undangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    /**
     * Definisi tiap KOLOM di tabel Summary: label tampilan, warna bar, dan
     * query yang menghasilkan baris [tahun, bulan, total] untuk kolom itu.
     *
     * PENTING: beberapa query di bawah ini masih ASUMSI nama kolom, karena
     * model sumbernya pakai $guarded (kolom aslinya belum saya ketahui pasti).
     * Ditandai dengan komentar "// ASUMSI" — cek & sesuaikan ke nama kolom
     * yang sebenarnya ada di database kamu.
     */
    protected function metricDefinitions(): array
    {
        return [
            'perizinan_terbit' => [
                'label' => 'Dokumen Perizinan Terbit',
                'color' => '#8B5E3C',
                'query' => fn () => PerizinanTerbit::query()
                    // ASUMSI: kolom tanggal terbit izin bernama 'tanggal_sejak'
                    ->selectRaw('YEAR(tanggal_sejak) as tahun, MONTH(tanggal_sejak) as bulan, COUNT(*) as total')
                    ->whereNotNull('tanggal_sejak')
                    ->groupBy('tahun', 'bulan'),
            ],
            'pelaporan_eksternal' => [
                'label' => 'Pelaporan Korporasi Eksternal',
                'color' => '#9CAF88',
                'query' => fn () => Pelaporan::query()
                    // KONFIRMASI: kolom 'tujuan' enum('Eksternal','Internal'), kolom tanggal 'tanggal'
                    ->where('tujuan', 'Eksternal')
                    ->selectRaw('YEAR(tanggal) as tahun, MONTH(tanggal) as bulan, COUNT(*) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'ba_rapat' => [
                'label' => 'BA Rapat Monitoring Kinerja Terbit',
                'color' => '#E96B6B',
                'query' => fn () => BarSkMemoRapat::query()
                    // KONFIRMASI: kolom tanggal rapat bernama 'tanggal_rapat'
                    ->selectRaw('YEAR(tanggal_rapat) as tahun, MONTH(tanggal_rapat) as bulan, COUNT(*) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'memo_direksi' => [
                'label' => 'Memo Direksi Terbit',
                'color' => '#8EC5D6',
                'query' => fn () => MemoTerbit::query()
                    // KONFIRMASI: kolom tanggal bernama 'tanggal'
                    ->selectRaw('YEAR(tanggal) as tahun, MONTH(tanggal) as bulan, COUNT(*) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'sk_direksi' => [
                'label' => 'SK Direksi Terbit',
                'color' => '#B9A6D9',
                'query' => fn () => SkdTerbit::query()
                    // KONFIRMASI: kolom tanggal bernama 'tanggal_penetapan'
                    ->selectRaw('YEAR(tanggal_penetapan) as tahun, MONTH(tanggal_penetapan) as bulan, COUNT(*) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'mailroom' => [
                'label' => 'Penerimaan Mailroom',
                'color' => '#F2D06B',
                'query' => fn () => PengirimanDokumen::query()
                    // Kolom ini SUDAH PASTI namanya (sesuai model PengirimanDokumen).
                    // Catatan: kolom 'bulan' di sini berisi TEKS nama bulan Indonesia
                    // (bukan tanggal), jadi konversi ke angka bulan dilakukan di PHP,
                    // bukan di query — lihat method normalisasiBulan() di bawah.
                    ->selectRaw('tahun, bulan, SUM(penerimaan_mailroom) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'ekspedisi' => [
                'label' => 'Ekspedisi Pengiriman',
                'color' => '#C2588A',
                'query' => fn () => PengirimanDokumen::query()
                    ->selectRaw('tahun, bulan, SUM(pengiriman_dalam_negeri + pengiriman_luar_negeri) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'undangan_internal' => [
                'label' => 'Undangan Internal',
                'color' => '#3B6FA0',
                'query' => fn () => Undangan::query()
                    // KONFIRMASI: tabel 'undangan' sudah agregat bulanan (1 baris = 1 tahun+bulan),
                    // kolom 'undangan_intern' sudah langsung berisi totalnya.
                    ->selectRaw('tahun, bulan, SUM(undangan_intern) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'undangan_external' => [
                'label' => 'Undangan External',
                'color' => '#F2A65A',
                'query' => fn () => Undangan::query()
                    ->selectRaw('tahun, bulan, SUM(undangan_ekstern) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'dof' => [
                'label' => 'Pengelolaan DOF',
                'color' => '#7FC8A9',
                'query' => fn () => DofData::query()
                    // KONFIRMASI: tabel 'dof_data' sudah agregat bulanan, kolom nilainya 'jumlah'.
                    // ⚠️ WASPADA DUPLIKAT: 'rekap_masters' JUGA punya baris "DOF" (id 6) yang
                    // datanya tersimpan di rekap_data, terpisah dari tabel dof_data ini. Kalau
                    // ternyata modul "DOF" di aplikasi kamu sekarang input datanya lewat rekap_data
                    // (bukan dof_data), kabari saya — sumbernya perlu dipindah.
                    ->selectRaw('tahun, bulan, SUM(jumlah) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'arsip' => [
                'label' => 'Pengelolaan Dokumen Pusat Arsip',
                'color' => '#6B7280',
                'query' => fn () => RekapData::query()
                    // KONFIRMASI: tabel 'rekap_data' generik, dipakai bersama untuk 7 topik
                    // berbeda (Pusat Arsip, Teknikal File, Approval File Scan, Approval Stampel
                    // Digital, Digital Signature, DOF, E-materai — lihat 'rekap_masters').
                    // WAJIB difilter spesifik ke "Pusat Arsip" saja, supaya tidak ikut
                    // menjumlahkan 6 topik lain yang kebetulan satu tabel.
                    ->whereHas('masterRekap', fn ($q) => $q->where('nama_kegiatan', 'Pusat Arsip'))
                    ->selectRaw('tahun, bulan, SUM(jumlah) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
            'teknikal_file' => [
                'label' => 'Pengelolaan Dokumen Teknikal File',
                'color' => '#D96C6C',
                'query' => fn () => PaTeknikData::query()
                    // KONFIRMASI struktur tabel (sama pola dengan dof_data). Menjumlahkan
                    // SEMUA baris pa_teknik_data sebagai representasi "Teknikal File".
                    // ⚠️ WASPADA DUPLIKAT: 'rekap_masters' JUGA punya baris "Teknikal File"
                    // (id 2) yang datanya di rekap_data, terpisah dari tabel pa_teknik_data ini.
                    // Kalau modul "Teknikal File" input datanya lewat rekap_data (bukan
                    // pa_teknik_data), kabari saya — sumbernya perlu dipindah.
                    ->selectRaw('tahun, bulan, SUM(jumlah) as total')
                    ->groupBy('tahun', 'bulan'),
            ],
        ];
    }

    protected function bulanIndo(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    public function index(Request $request)
    {
        $selectedYear = $request->input('year', 'all');
        $bulanIndo = $this->bulanIndo();
        $metrics = $this->metricDefinitions();

        // Kumpulkan hasil semua query ke satu struktur: $data[tahun][bulan_angka][metric_key] = total
        $data = [];
        $tahunTerdeteksi = [];

        foreach ($metrics as $key => $def) {
            try {
                $rows = $def['query']()->get();
            } catch (\Throwable $e) {
                // Kalau ada kolom yang ternyata belum sesuai (masih ASUMSI), jangan sampai
                // seluruh halaman Summary ikut error — cukup kolom itu kosong dan lanjut.
                $rows = collect();
            }

            foreach ($rows as $row) {
                $tahun = (int) $row->tahun;

                // Normalisasi kolom 'bulan': bisa berupa ANGKA (hasil MONTH() dari query
                // berbasis tanggal) atau TEKS nama bulan Indonesia (dari PengirimanDokumen,
                // yang kolom 'bulan'-nya memang berisi string "Januari", "Februari", dst).
                if (is_numeric($row->bulan)) {
                    $bulanAngka = (int) $row->bulan;
                } else {
                    $bulanAngka = array_search($row->bulan, $bulanIndo, true);
                    if ($bulanAngka === false) {
                        continue; // nama bulan tidak dikenali, lewati baris ini
                    }
                }

                if ($selectedYear !== 'all' && $tahun !== (int) $selectedYear) {
                    continue;
                }

                $tahunTerdeteksi[$tahun] = true;
                $data[$tahun][$bulanAngka][$key] = (int) $row->total;
            }
        }

        $availableYears = collect(array_keys($tahunTerdeteksi))->sortDesc()->values();

        // Susun jadi baris tabel terurut kronologis (tahun lama -> baru, Jan -> Des)
        $tableRows = collect();
        foreach (collect(array_keys($data))->sort() as $tahun) {
            foreach ($bulanIndo as $bulanAngka => $namaBulan) {
                if (!isset($data[$tahun][$bulanAngka])) {
                    continue;
                }

                $rowValues = [];
                foreach (array_keys($metrics) as $key) {
                    $rowValues[$key] = $data[$tahun][$bulanAngka][$key] ?? 0;
                }

                $tableRows->push([
                    'tahun' => $tahun,
                    'bulan' => $namaBulan,
                    'values' => $rowValues,
                ]);
            }
        }

        // Total & nilai maksimum per kolom (dipakai untuk lebar bar visual & baris "Total keseluruhan")
        $totals = [];
        $maxPerColumn = [];
        foreach (array_keys($metrics) as $key) {
            $columnValues = $tableRows->pluck("values.$key");
            $totals[$key] = $columnValues->sum();
            $maxPerColumn[$key] = max($columnValues->max(), 1); // minimal 1 agar tidak dibagi nol
        }

        return view('summary.index', [
            'metrics' => $metrics,
            'tableRows' => $tableRows,
            'totals' => $totals,
            'maxPerColumn' => $maxPerColumn,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
        ]);
    }
}