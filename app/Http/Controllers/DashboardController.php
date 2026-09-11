<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $bulanIndo = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * Jalankan blok pengambilan data dengan aman. Jika ada modul yang gagal
     * (tabel belum ada, dsb), dashboard tetap tampil dengan nilai default,
     * bukan crash total.
     */
    private function safe(callable $fn, $default, string $label)
    {
        try {
            return $fn();
        } catch (\Throwable $e) {
            Log::warning("[Dashboard] Gagal ambil data $label: " . $e->getMessage());
            return $default;
        }
    }

    public function index()
    {
        $hariIni = Carbon::now();
        $tahunIni = $hariIni->year;
        $bulanIni = $this->bulanIndo[$hariIni->month];
        $batasWaktu = Carbon::now()->addDays(30);

        // ==========================================
        // 1. KARYAWAN (data master, point-in-time)
        // ==========================================
        $karyawanStats = $this->safe(function () use ($tahunIni, $bulanIni) {
            $data = KaryawanController::getReportData($tahunIni, $bulanIni);
            return [
                'total' => $data['karyawan']->count(),
                'organik' => $data['countOrganik'],
                'nonOrganik' => $data['countNonOrganik'],
                'pensiun' => $data['chartPensiunData'],
            ];
        }, ['total' => 0, 'organik' => 0, 'nonOrganik' => 0, 'pensiun' => ['Sudah Pensiun' => 0, '< 5 Tahun' => 0, '< 10 Tahun' => 0, '> 10 Tahun' => 0]], 'Karyawan');

        // ==========================================
        // 2. KETIDAKHADIRAN (bulan berjalan)
        // ==========================================
        $totalKetidakhadiran = $this->safe(function () use ($hariIni) {
            return DB::table('ketidakhadiran')
                ->whereMonth('created_at', $hariIni->month)
                ->whereYear('created_at', $hariIni->year)
                ->count();
        }, 0, 'Ketidakhadiran');

        // ==========================================
        // 3. ANGGARAN (per kategori: Dikelola, Rutin, Investasi)
        // ==========================================
        $anggaranStats = $this->safe(function () use ($tahunIni, $bulanIni) {
            $data = AnggaranController::getReportData($tahunIni, $bulanIni);
            $perKategori = [];
            foreach ($data['kategoriList'] as $kode => $label) {
                $items = $data['anggaran']->where('kategori', $kode);
                $rkap = $items->sum('rkap');
                $realKomit = $items->sum('realisasi') + $items->sum('komitmen');
                $perKategori[$kode] = [
                    'label' => $label,
                    'rkap' => $rkap,
                    'realisasi_komitmen' => $realKomit,
                    'sisa' => $data['sisaPerKategori'][$kode] ?? ($rkap - $realKomit),
                    'persen' => $rkap > 0 ? round($realKomit / $rkap * 100, 1) : 0,
                ];
            }
            return [
                'persenTotal' => $data['percRealisasiKomitmen'],
                'totalRkap' => $data['totalRkap'],
                'totalRealKomit' => $data['totalRealPlusKomit'],
                'totalSisa' => $data['totalSisa'],
                'perKategori' => $perKategori,
            ];
        }, ['persenTotal' => 0, 'totalRkap' => 0, 'totalRealKomit' => 0, 'totalSisa' => 0, 'perKategori' => []], 'Anggaran');

        // ==========================================
        // 4. PERIZINAN PERKANTORAN
        // ==========================================
        $perizinanStats = $this->safe(function () use ($tahunIni, $bulanIni) {
            $data = PerizinanPerkantoranController::getReportData($tahunIni, $bulanIni);
            return [
                'bulanIni' => $data['ringkasanTerbit']->total_terbit ?? 0,
                'ringkasan' => $data['ringkasanTerbit'],
                'statistikSemuaTahun' => $data['statistikTerbitSemuaTahun'],
            ];
        }, ['bulanIni' => 0, 'ringkasan' => null, 'statistikSemuaTahun' => collect()], 'Perizinan');

        // ==========================================
        // 5. PELAPORAN
        // ==========================================
        $pelaporanStats = $this->safe(function () use ($tahunIni, $bulanIni) {
            $data = PelaporanController::getReportData($tahunIni, $bulanIni);
            return [
                'eksternal' => $data['totalEksternal'],
                'internal' => $data['totalInternal'],
                'total' => $data['totalEksternal'] + $data['totalInternal'],
            ];
        }, ['eksternal' => 0, 'internal' => 0, 'total' => 0], 'Pelaporan');

        // ==========================================
        // 6. PROGRAM STRATEGIS (bulan berjalan)
        // ==========================================
        $programStrategisTotal = $this->safe(function () use ($tahunIni, $bulanIni) {
            $data = ProgramStrategisController::getReportData($tahunIni, $bulanIni);
            return $data['rawData']->count();
        }, 0, 'Program Strategis');

        // ==========================================
        // 7. KEARSIPAN — dipecah per sub-menu
        // ==========================================
        $kearsipanStats = $this->safe(function () use ($tahunIni, $bulanIni) {
            $paTekstual = PaTekstualController::getReportData($tahunIni, $bulanIni)['paTekstual'];
            $totalPaTekstual = $paTekstual['tabel1']->sum('jumlah') + $paTekstual['tabel2']->sum('jumlah');

            $paNonTekstual = PaNonTekstualController::getReportData($tahunIni, $bulanIni)['paNonTekstual'];
            $totalPaNonTekstual = $paNonTekstual->sum('jumlah');

            $paTeknik = PaTeknikController::getReportData($tahunIni, $bulanIni)['paTeknik'];
            $totalPaTeknik = $paTeknik['tabel1']->sum('jumlah') + $paTeknik['tabel2']->sum('jumlah');

            $dof = DofController::getReportData($tahunIni, $bulanIni)['dof'];
            $totalDof = $dof['tabel1']->sum('jumlah') + $dof['tabel2']->sum('jumlah');

            return [
                'paTekstual' => $totalPaTekstual,
                'paNonTekstual' => $totalPaNonTekstual,
                'paTeknik' => $totalPaTeknik,
                'dof' => $totalDof,
                'total' => $totalPaTekstual + $totalPaNonTekstual + $totalPaTeknik + $totalDof,
            ];
        }, ['paTekstual' => 0, 'paNonTekstual' => 0, 'paTeknik' => 0, 'dof' => 0, 'total' => 0], 'Kearsipan');

        // ==========================================
        // 8. ADMINISTRASI PERKANTORAN — dipecah per sub-menu
        // ==========================================
        $administrasiStats = $this->safe(function () use ($tahunIni, $bulanIni) {
            $pemeliharaan = PemeliharaanController::getReportData($tahunIni, $bulanIni);
            $totalPemeliharaan = $pemeliharaan['pemeliharaanRutin']->sum('jumlah') + $pemeliharaan['pemeliharaanPeralatan']->sum('jumlah');

            $pengiriman = PengirimanDokumenController::getReportData($tahunIni, $bulanIni);
            $vol = $pengiriman['pengirimanVolume'];
            $totalPengiriman = $vol ? ($vol->penerimaan_mailroom + $vol->pengiriman_dalam_negeri + $vol->pengiriman_luar_negeri + $vol->registrasi_surat_masuk_dof) : 0;

            $kurirData = JasaKurirController::getReportData($tahunIni, $bulanIni);
            $totalKurir = collect($kurirData['tableData'])->first()['total_semua'] ?? 0;

            $fotocopy = JasaFotocopyController::getReportData($tahunIni, $bulanIni);
            $totalFotocopy = $fotocopy['totalPemakaian'];

            $undangan = UndanganController::getReportData($tahunIni, $bulanIni);
            $totalUndangan = $undangan['totalIntern'] + $undangan['totalEkstern'];

            $surat = SuratController::getReportData($tahunIni, $bulanIni);
            $totalSurat = collect($surat['rekapData'])->sum('total_masuk') + collect($surat['rekapData'])->sum('total_keluar');

            $barSkMemoObj = BarSkMemoController::getReportData($tahunIni, $bulanIni)['barSkMemo'];
            $totalBarSkMemo = 0;
            if ($barSkMemoObj) {
                $totalBarSkMemo = ($barSkMemoObj->skd_keputusan_bersama_terbit ?? 0) + ($barSkMemoObj->skd_non_ratifikasi_terbit ?? 0)
                    + ($barSkMemoObj->skd_ratifikasi_terbit ?? 0) + ($barSkMemoObj->memo_direksi_terbit ?? 0)
                    + ($barSkMemoObj->bar_monitoring_terbit ?? 0) + ($barSkMemoObj->bar_manajemen_terbit ?? 0);
            }

            return [
                'pemeliharaan' => $totalPemeliharaan,
                'pengiriman' => $totalPengiriman,
                'kurir' => $totalKurir,
                'fotocopy' => $totalFotocopy,
                'undangan' => $totalUndangan,
                'surat' => $totalSurat,
                'barSkMemo' => $totalBarSkMemo,
                'total' => $totalPemeliharaan + $totalPengiriman + $totalKurir + $totalFotocopy + $totalUndangan + $totalSurat + $totalBarSkMemo,
            ];
        }, ['pemeliharaan' => 0, 'pengiriman' => 0, 'kurir' => 0, 'fotocopy' => 0, 'undangan' => 0, 'surat' => 0, 'barSkMemo' => 0, 'total' => 0], 'Administrasi');

        // ==========================================
        // 9. PANEL ALERT & PENGINGAT
        // ==========================================
        $alertPerizinan = $this->safe(function () use ($hariIni, $batasWaktu) {
            return DB::table('perizinan_terbit')
                ->where('tanggal_akhir', '>=', $hariIni)
                ->where('tanggal_akhir', '<=', $batasWaktu)
                ->orderBy('tanggal_akhir', 'asc')
                ->take(5)
                ->get();
        }, collect(), 'Alert Perizinan');

        $alertRapat = $this->safe(function () {
            return DB::table('bar_sk_memo_rapat')
                ->where('status', '!=', 'Selesai')
                ->orderBy('tanggal_rapat', 'desc')
                ->take(5)
                ->get();
        }, collect(), 'Alert Rapat');

        return view('dashboard', compact(
            'bulanIni', 'tahunIni',
            'karyawanStats', 'totalKetidakhadiran', 'anggaranStats',
            'perizinanStats', 'pelaporanStats', 'programStrategisTotal',
            'kearsipanStats', 'administrasiStats',
            'alertPerizinan', 'alertRapat'
        ));
    }
}