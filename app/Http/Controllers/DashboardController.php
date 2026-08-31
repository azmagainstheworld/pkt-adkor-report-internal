<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::now();
        $bulanIni = $hariIni->month;
        $tahunIni = $hariIni->year;
        $batasWaktu = Carbon::now()->addDays(30);

        // ==========================================
        // 1. KARYAWAN (Tabel: karyawan)
        // ==========================================
        $totalKaryawan = DB::table('karyawan')->count();
        
        // Kita hitung Organik vs Non-Organik (Asumsi kolomnya bernama 'status' atau 'jenis_karyawan')
        // Trik: Jika kolom belum ada, kita kasih nilai 0 agar tidak error
        $karyawanOrganik = 0;
        if (Schema::hasColumn('karyawan', 'status')) {
            $karyawanOrganik = DB::table('karyawan')->where('status', 'like', '%Organik%')->count();
        } elseif (Schema::hasColumn('karyawan', 'jenis_karyawan')) {
            $karyawanOrganik = DB::table('karyawan')->where('jenis_karyawan', 'like', '%Organik%')->count();
        }
        $karyawanNonOrganik = $totalKaryawan - $karyawanOrganik;

        // ==========================================
        // 2. KETIDAKHADIRAN (Tabel: ketidakhadiran)
        // ==========================================
        $totalKetidakhadiran = DB::table('ketidakhadiran')
            ->whereMonth('created_at', $bulanIni)
            ->whereYear('created_at', $tahunIni)
            ->count();

        // ==========================================
        // 3. ANGGARAN (Tabel: anggaran)
        // ==========================================
        $persenAnggaran = 0;
        // Asumsi standar: kolom pagu dan realisasi. Jika tabelnya beda, ini tetap aman.
        if (Schema::hasColumn('anggaran', 'pagu') && Schema::hasColumn('anggaran', 'realisasi')) {
            $totalPagu = DB::table('anggaran')->where('tahun', $tahunIni)->sum('pagu');
            $totalRealisasi = DB::table('anggaran')->where('tahun', $tahunIni)->sum('realisasi');
            if ($totalPagu > 0) {
                $persenAnggaran = round(($totalRealisasi / $totalPagu) * 100);
            }
        }

        // ==========================================
        // 4. PERIZINAN PERKANTORAN (Tabel: perizinan_terbit)
        // ==========================================
        $perizinanBulanIni = DB::table('perizinan_terbit')
            ->whereMonth('tanggal_sejak', $bulanIni)
            ->whereYear('tanggal_sejak', $tahunIni)
            ->count();

        // ==========================================
        // 5. PELAPORAN (Tabel: pelaporan)
        // ==========================================
        $pelaporanEksternal = DB::table('pelaporan')->where('tujuan', 'Eksternal')->count();
        $pelaporanInternal = DB::table('pelaporan')->where('tujuan', 'Internal')->count();
        $totalPelaporan = $pelaporanEksternal + $pelaporanInternal;

        // ==========================================
        // 6. KEARSIPAN (Gabungan Tabel Data)
        // ==========================================
        $totalKearsipan = 0;
        $tabelKearsipan = ['pa_tekstual_data', 'pa_non_tekstual_data', 'pa_teknik_data', 'dof_data'];
        foreach ($tabelKearsipan as $tabel) {
            // Loop canggih: Cek apakah tabelnya ada, lalu jumlahkan nilainya
            if (Schema::hasTable($tabel) && Schema::hasColumn($tabel, 'jumlah')) {
                $totalKearsipan += DB::table($tabel)->sum('jumlah');
            }
        }

        // ==========================================
        // 7. ADMINISTRASI (Gabungan Aktivitas)
        // ==========================================
        $totalAdministrasi = 0;
        $tabelAdministrasi = ['pengiriman_dokumen', 'jasa_kurir_data', 'undangan'];
        foreach ($tabelAdministrasi as $tabel) {
            if (Schema::hasTable($tabel)) {
                $totalAdministrasi += DB::table($tabel)
                    ->whereMonth('created_at', $bulanIni)
                    ->whereYear('created_at', $tahunIni)
                    ->count();
            }
        }

        // ==========================================
        // 8. PANEL ALERT & PENGINGAT
        // ==========================================
        $alertPerizinan = DB::table('perizinan_terbit')
            ->where('tanggal_akhir', '>=', $hariIni)
            ->where('tanggal_akhir', '<=', $batasWaktu)
            ->orderBy('tanggal_akhir', 'asc')
            ->take(5)
            ->get();

        $alertRapat = DB::table('bar_sk_memo_rapat')
            ->where('status', '!=', 'Selesai')
            ->orderBy('tanggal_rapat', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalKaryawan', 'karyawanOrganik', 'karyawanNonOrganik',
            'totalKetidakhadiran', 'persenAnggaran', 'perizinanBulanIni',
            'totalPelaporan', 'pelaporanEksternal', 'pelaporanInternal',
            'totalKearsipan', 'totalAdministrasi',
            'alertPerizinan', 'alertRapat'
        ));
    }
}
