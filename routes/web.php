<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KetidakhadiranController;
use App\Http\Controllers\PerizinanPerkantoranController;
use App\Http\Controllers\PelaporanController; 
use App\Http\Controllers\JasaKurirController;
use App\Http\Controllers\UndanganController;
use App\Http\Controllers\PemeliharaanController; 
use App\Http\Controllers\BarSkMemoController;
use App\Http\Controllers\MasalahKendalaController;
use App\Http\Controllers\PaTekstualController;
use App\Http\Controllers\PaNonTekstualController;
use App\Http\Controllers\PaTeknikController;
use App\Http\Controllers\DofController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\ProgramStrategisController;
use App\Http\Controllers\PengirimanDokumenController;
use App\Http\Controllers\AnggaranController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\StrukturOrganisasiController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratController; 
use App\Http\Controllers\CetakLaporanController; // Controller Baru Kita



// Rute untuk Lupa Password
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.update');
});

// 1. Rute Default (Root)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 2. Rute untuk tamu (Guest) yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
});

// 3. Rute untuk pengguna yang sudah berhasil login (Auth)
Route::middleware('auth')->group(function () {
    // Proses Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/summary', [SummaryController::class, 'index'])->name('summary.index');

    // ==========================================
    // RUTE CETAK LAPORAN BULANAN (HALAMAN KHUSUS)
    // ==========================================
    Route::get('/cetak-laporan', [CetakLaporanController::class, 'index'])->name('cetak.laporan.index');
    Route::post('/cetak-laporan', [CetakLaporanController::class, 'cetakPDF'])->name('cetak.laporan.pdf');
    // ==========================================
    // RUTE MODUL APLIKASI
    // ==========================================
    
    Route::get('/program-strategis', [ProgramStrategisController::class, 'index'])->name('program-strategis.index');
    Route::post('/program-strategis', [ProgramStrategisController::class, 'store'])->name('program-strategis.store');
    Route::put('/program-strategis/{id}', [ProgramStrategisController::class, 'update'])->name('program-strategis.update');
    Route::delete('/program-strategis/{id}', [ProgramStrategisController::class, 'destroy'])->name('program-strategis.destroy');
    Route::post('/program-strategis/import', [\App\Http\Controllers\ProgramStrategisController::class, 'import'])->name('program-strategis.import');
    Route::get('/program-strategis/export/excel', [\App\Http\Controllers\ProgramStrategisController::class, 'exportExcel'])->name('program-strategis.export.excel');
    Route::get('/program-strategis/export/pdf', [\App\Http\Controllers\ProgramStrategisController::class, 'exportPdf'])->name('program-strategis.export.pdf');
    Route::post('/program-strategis/kolom', [\App\Http\Controllers\ProgramStrategisController::class, 'storeKolomDinamis'])->name('program-strategis.kolom.store');
    Route::delete('/program-strategis/kolom/{id}', [\App\Http\Controllers\ProgramStrategisController::class, 'destroyKolomDinamis'])->name('program-strategis.kolom.destroy');

    // Karyawan Resource
    Route::resource('karyawan', KaryawanController::class);
    
    // ==========================================
    // MANAJEMEN KELUARGA KARYAWAN (ANTI-ERROR)
    // ==========================================
    // Rute Fallback: Jika user me-refresh halaman post, otomatis dipantulkan kembali
    Route::get('/karyawan/{karyawan}/keluarga', function ($karyawan) {
        return redirect()->route('karyawan.show', $karyawan);
    });
    Route::post('/karyawan/{karyawan}/keluarga', [KaryawanController::class, 'storeKeluarga'])->name('keluarga.store');

    // Rute Fallback untuk Edit & Hapus
    Route::get('/keluarga/{keluarga}', function ($keluarga) {
        $kel = \App\Models\KeluargaKaryawan::find($keluarga);
        return $kel ? redirect()->route('karyawan.show', $kel->karyawan_id) : redirect()->route('karyawan.index');
    });
    Route::put('/keluarga/{keluarga}', [KaryawanController::class, 'updateKeluarga'])->name('keluarga.update');
    Route::delete('/keluarga/{keluarga}', [KaryawanController::class, 'destroyKeluarga'])->name('keluarga.destroy');

    Route::post('/karyawan/import', [KaryawanController::class, 'import'])->name('karyawan.import');
    Route::post('/karyawan/import-keluarga', [KaryawanController::class, 'importKeluarga'])->name('karyawan.keluarga.import');
    Route::post('/karyawan/kolom-dinamis', [KaryawanController::class, 'storeKolomDinamis'])->name('karyawan.kolom.store');
    Route::delete('/karyawan/kolom-dinamis/{id}', [KaryawanController::class, 'destroyKolomDinamis'])->name('karyawan.kolom.destroy');
    Route::get('/karyawan/export/excel', [KaryawanController::class, 'exportExcel'])->name('karyawan.export.excel');
    Route::get('/karyawan/export/pdf', [KaryawanController::class, 'exportPdf'])->name('karyawan.export.pdf');
    Route::get('/karyawan/template/excel', [KaryawanController::class, 'downloadTemplate'])->name('karyawan.template.excel');

    // Ketidakhadiran
    Route::get('/ketidakhadiran', [KetidakhadiranController::class, 'index'])
        ->name('ketidakhadiran.index');
    
    Route::post('/ketidakhadiran/bulanan', [KetidakhadiranController::class, 'storeBulanan'])
        ->name('ketidakhadiran.storeBulanan');
    
    Route::delete('/ketidakhadiran/bulanan/{ketidakhadiran}', [KetidakhadiranController::class, 'destroyBulanan'])
        ->name('ketidakhadiran.destroyBulanan');
    
    Route::post('/ketidakhadiran/harian', [KetidakhadiranController::class, 'storeHarian'])
        ->name('ketidakhadiran.storeHarian');
    
    Route::delete('/ketidakhadiran/harian/{harian}', [KetidakhadiranController::class, 'destroyHarian'])
        ->name('ketidakhadiran.destroyHarian');
    
    Route::get('/ketidakhadiran/harian', [KetidakhadiranController::class, 'harian'])
        ->name('ketidakhadiran.harian');

    Route::post('/ketidakhadiran/import', [KetidakhadiranController::class, 'import'])->name('ketidakhadiran.import');
    Route::get('/ketidakhadiran/export/excel', [KetidakhadiranController::class, 'exportExcel'])->name('ketidakhadiran.export.excel');
    Route::get('/ketidakhadiran/export/pdf', [KetidakhadiranController::class, 'exportPdf'])->name('ketidakhadiran.export.pdf');
    Route::get('/ketidakhadiran/template/excel', [KetidakhadiranController::class, 'downloadTemplate'])->name('ketidakhadiran.template.excel');

    // Anggaran Administrasi
    Route::get('/anggaran', [AnggaranController::class, 'index'])->name('anggaran.index');
    Route::post('/anggaran', [AnggaranController::class, 'store'])->name('anggaran.store');
    Route::put('/anggaran/{anggaran}', [AnggaranController::class, 'update'])->name('anggaran.update');
    Route::delete('/anggaran/{anggaran}', [AnggaranController::class, 'destroy'])->name('anggaran.destroy');
    Route::post('/anggaran/import', [AnggaranController::class, 'import'])->name('anggaran.import');
    Route::get('/anggaran/export/excel', [AnggaranController::class, 'exportExcel'])->name('anggaran.export.excel');
    Route::get('/anggaran/export/pdf', [AnggaranController::class, 'exportPdf'])->name('anggaran.export.pdf');
    Route::get('/anggaran/template/excel', [AnggaranController::class, 'downloadTemplate'])->name('anggaran.template.excel');


    // Perizinan Perkantoran
    Route::get('/perizinan-perkantoran', [PerizinanPerkantoranController::class, 'index'])->name('perizinan-perkantoran.index');
    Route::post('/perizinan-perkantoran/store', [PerizinanPerkantoranController::class, 'store'])->name('perizinan-perkantoran.store');
    Route::put('/perizinan-perkantoran/{id}', [PerizinanPerkantoranController::class, 'update'])->name('perizinan-perkantoran.update');
    Route::delete('/perizinan-perkantoran/{id}', [PerizinanPerkantoranController::class, 'destroy'])->name('perizinan-perkantoran.destroy');

    Route::post('/perizinan-proses-list', [PerizinanPerkantoranController::class, 'storeProses'])->name('perizinan-proses.store');
    Route::put('/perizinan-proses-list/{id}', [PerizinanPerkantoranController::class, 'updateProses'])->name('perizinan-proses.update');
    Route::delete('/perizinan-proses-list/{id}', [PerizinanPerkantoranController::class, 'destroyProses'])->name('perizinan-proses.destroy');

    Route::post('/perizinan-perkantoran/import', [PerizinanPerkantoranController::class, 'import'])->name('perizinan-perkantoran.import');
    Route::get('/perizinan-perkantoran/export/excel', [PerizinanPerkantoranController::class, 'exportExcel'])->name('perizinan-perkantoran.export.excel');
    Route::get('/perizinan-perkantoran/export/pdf', [PerizinanPerkantoranController::class, 'exportPdf'])->name('perizinan-perkantoran.export.pdf');
    Route::get('/perizinan-perkantoran/template/excel', [PerizinanPerkantoranController::class, 'downloadTemplate'])->name('perizinan-perkantoran.template.excel');


    Route::post('/perizinan-proses/import', [PerizinanPerkantoranController::class, 'importProses'])->name('perizinan-proses.import');
    Route::get('/perizinan-proses/export/excel', [PerizinanPerkantoranController::class, 'exportExcelProses'])->name('perizinan-proses.export.excel');
    Route::get('/perizinan-proses/export/pdf', [PerizinanPerkantoranController::class, 'exportPdfProses'])->name('perizinan-proses.export.pdf');
    Route::get('/perizinan-proses/template/excel', [PerizinanPerkantoranController::class, 'downloadTemplateProses'])->name('perizinan-proses.template.excel');

    // Menu Struktur Organisasi
    Route::get('/struktur-organisasi', [StrukturOrganisasiController::class, 'index'])->name('struktur-organisasi.index');
    Route::post('/struktur-organisasi', [StrukturOrganisasiController::class, 'update'])->name('struktur-organisasi.update');

    // Pelaporan
    Route::get('/pelaporan', [PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::post('/pelaporan', [PelaporanController::class, 'store'])->name('pelaporan.store');
    Route::put('/pelaporan/{id}', [PelaporanController::class, 'update'])->name('pelaporan.update');
    Route::delete('/pelaporan/{id}', [PelaporanController::class, 'destroy'])->name('pelaporan.destroy');
    Route::post('/pelaporan/import', [PelaporanController::class, 'import'])->name('pelaporan.import');
    Route::get('/pelaporan/export/excel', [PelaporanController::class, 'exportExcel'])->name('pelaporan.export.excel');
    Route::get('/pelaporan/export/pdf', [PelaporanController::class, 'exportPdf'])->name('pelaporan.export.pdf');
    Route::get('/pelaporan/template/excel', [PelaporanController::class, 'downloadTemplate'])->name('pelaporan.template.excel');

    // Rute Pengaturan Kolom Dinamis (Pelaporan)
    Route::post('/kolom-dinamis', [PelaporanController::class, 'storeKolomDinamis'])->name('kolom-dinamis.store');
    Route::delete('/kolom-dinamis/{id}', [PelaporanController::class, 'destroyKolomDinamis'])->name('kolom-dinamis.destroy');

    Route::get('/masalah-kendala', [MasalahKendalaController::class, 'index'])->name('masalah-kendala.index');
    Route::post('/masalah-kendala', [MasalahKendalaController::class, 'store'])->name('masalah-kendala.store');
    Route::put('/masalah-kendala/{id}', [MasalahKendalaController::class, 'update'])->name('masalah-kendala.update');
    Route::delete('/masalah-kendala/{id}', [MasalahKendalaController::class, 'destroy'])->name('masalah-kendala.destroy');
    Route::post('/masalah-kendala/import', [MasalahKendalaController::class, 'import'])->name('masalah-kendala.import');
    Route::get('/masalah-kendala/export/excel', [MasalahKendalaController::class, 'exportExcel'])->name('masalah-kendala.export.excel');
    Route::get('/masalah-kendala/export/pdf', [MasalahKendalaController::class, 'exportPdf'])->name('masalah-kendala.export.pdf');
    Route::get('/masalah-kendala/template/excel', [MasalahKendalaController::class, 'downloadTemplate'])->name('masalah-kendala.template.excel');

    // --- Grup Kearsipan ---

    // PA Tekstual
    Route::get('/kearsipan/pa-non-teknik/tekstual', [PaTekstualController::class, 'index'])->name('pa-tekstual.index');
    Route::post('/kearsipan/pa-non-teknik/tekstual/master', [PaTekstualController::class, 'storeMaster'])->name('pa-tekstual.storeMaster');
    Route::delete('/kearsipan/pa-non-teknik/tekstual/master/{id}', [PaTekstualController::class, 'destroyMaster'])->name('pa-tekstual.destroyMaster');
    Route::post('/kearsipan/pa-non-teknik/tekstual/dokumen', [PaTekstualController::class, 'storeDokumen'])->name('pa-tekstual.storeDokumen');
    Route::post('/kearsipan/pa-non-teknik/tekstual/update-bulan', [PaTekstualController::class, 'updateBulan'])->name('pa-tekstual.updateBulan');
    Route::delete('/kearsipan/pa-non-teknik/tekstual/destroy-bulan', [PaTekstualController::class, 'destroyBulan'])->name('pa-tekstual.destroyBulan');
    Route::post('/kearsipan/pa-non-teknik/tekstual/import', [PaTekstualController::class, 'importExcel'])->name('pa-tekstual.import');
    Route::get('/kearsipan/pa-non-teknik/tekstual/export/excel', [PaTekstualController::class, 'exportExcel'])->name('pa-tekstual.export.excel');
    Route::get('/kearsipan/pa-non-teknik/tekstual/export/pdf', [PaTekstualController::class, 'exportPdf'])->name('pa-tekstual.export.pdf');
    // ==========================================
    // MENU NON TEKNIK NON TEKSTUAL (ARSIP) - STRUKTUR DINAMIS
    // ==========================================
    Route::get('/non-teknik-non-tekstual', [PaNonTekstualController::class, 'index'])->name('non-teknik-non-tekstual.index');
    Route::post('/non-teknik-non-tekstual/master', [PaNonTekstualController::class, 'storeMaster'])->name('non-teknik-non-tekstual.storeMaster');
    Route::delete('/non-teknik-non-tekstual/master/{id}', [PaNonTekstualController::class, 'destroyMaster'])->name('non-teknik-non-tekstual.destroyMaster');
    Route::post('/non-teknik-non-tekstual/dokumen', [PaNonTekstualController::class, 'storeDokumen'])->name('non-teknik-non-tekstual.storeDokumen');
    Route::post('/non-teknik-non-tekstual/update-bulan', [PaNonTekstualController::class, 'updateBulan'])->name('non-teknik-non-tekstual.updateBulan');
    Route::delete('/non-teknik-non-tekstual/destroy-bulan', [PaNonTekstualController::class, 'destroyBulan'])->name('non-teknik-non-tekstual.destroyBulan');

    Route::post('/non-teknik-non-tekstual/import', [PaNonTekstualController::class, 'importExcel'])->name('non-teknik-non-tekstual.import');
    Route::get('/non-teknik-non-tekstual/export/excel', [PaNonTekstualController::class, 'exportExcel'])->name('non-teknik-non-tekstual.export.excel');
    Route::get('/non-teknik-non-tekstual/export/pdf', [PaNonTekstualController::class, 'exportPdf'])->name('non-teknik-non-tekstual.export.pdf');
    
   
    // MENU PA TEKNIK
    Route::get('/kearsipan/pa-teknik', [PaTeknikController::class, 'index'])->name('pa-teknik.index');
    Route::post('/kearsipan/pa-teknik/master', [PaTeknikController::class, 'storeMaster'])->name('pa-teknik.storeMaster');
    Route::delete('/kearsipan/pa-teknik/master/{id}', [PaTeknikController::class, 'destroyMaster'])->name('pa-teknik.destroyMaster');
    Route::post('/kearsipan/pa-teknik/dokumen', [PaTeknikController::class, 'storeDokumen'])->name('pa-teknik.storeDokumen');
    Route::post('/kearsipan/pa-teknik/update-bulan', [PaTeknikController::class, 'updateBulan'])->name('pa-teknik.updateBulan');
    Route::delete('/kearsipan/pa-teknik/destroy-bulan', [PaTeknikController::class, 'destroyBulan'])->name('pa-teknik.destroyBulan');

    Route::post('/kearsipan/pa-teknik/import', [PaTeknikController::class, 'importExcel'])->name('pa-teknik.import');
    Route::get('/kearsipan/pa-teknik/export/excel', [PaTeknikController::class, 'exportExcel'])->name('pa-teknik.export.excel');
    Route::get('/kearsipan/pa-teknik/export/pdf', [PaTeknikController::class, 'exportPdf'])->name('pa-teknik.export.pdf');

    // MENU DOF
    Route::get('/kearsipan/dof', [DofController::class, 'index'])->name('dof.index');
    Route::post('/kearsipan/dof/master', [DofController::class, 'storeMaster'])->name('dof.storeMaster');
    Route::delete('/kearsipan/dof/master/{id}', [DofController::class, 'destroyMaster'])->name('dof.destroyMaster');
    Route::post('/kearsipan/dof/dokumen', [DofController::class, 'storeDokumen'])->name('dof.storeDokumen');
    Route::post('/kearsipan/dof/update-bulan', [DofController::class, 'updateBulan'])->name('dof.updateBulan');
    Route::delete('/kearsipan/dof/destroy-bulan', [DofController::class, 'destroyBulan'])->name('dof.destroyBulan');

    Route::post('/kearsipan/dof/import', [DofController::class, 'importExcel'])->name('dof.import');
    Route::get('/kearsipan/dof/export/excel', [DofController::class, 'exportExcel'])->name('dof.export.excel');
    Route::get('/kearsipan/dof/export/pdf', [DofController::class, 'exportPdf'])->name('dof.export.pdf');

    // MENU REKAP KEARSIPAN
    Route::get('/kearsipan/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::post('/kearsipan/rekap/data', [RekapController::class, 'storeData'])->name('rekap.storeData');
    Route::post('/kearsipan/rekap/update-bulan', [RekapController::class, 'updateBulan'])->name('rekap.updateBulan');
    Route::delete('/kearsipan/rekap/destroy-bulan', [RekapController::class, 'destroyBulan'])->name('rekap.destroyBulan');
    
    // Jasa Kurir
    Route::get('/administrasi/jasa-kurir', [JasaKurirController::class, 'index'])->name('jasakurir');
    Route::post('/administrasi/jasa-kurir/master', [JasaKurirController::class, 'storeMaster'])->name('jasakurir.master.store');
    Route::delete('/administrasi/jasa-kurir/master/{id}', [JasaKurirController::class, 'destroyMaster'])->name('jasakurir.master.destroy');
    Route::post('/administrasi/jasa-kurir/data', [JasaKurirController::class, 'storeData'])->name('jasakurir.data.store');
    Route::delete('/administrasi/jasa-kurir/data/{tahun}/{bulan}', [JasaKurirController::class, 'destroyData'])->name('jasakurir.data.destroy');
    Route::post('/administrasi/jasa-kurir/import', [JasaKurirController::class, 'import'])->name('jasakurir.import');
    Route::get('/administrasi/jasa-kurir/export/excel', [JasaKurirController::class, 'exportExcel'])->name('jasakurir.export.excel');
    Route::get('/administrasi/jasa-kurir/export/pdf', [JasaKurirController::class, 'exportPdf'])->name('jasakurir.export.pdf');
    Route::get('/administrasi/jasa-kurir/template', [JasaKurirController::class, 'downloadTemplate'])->name('jasakurir.download.template');

    // Pengiriman Dokumen
    Route::get('/administrasi/pengiriman-dokumen', [PengirimanDokumenController::class, 'index'])->name('pengiriman-dokumen.index');
    Route::post('/administrasi/pengiriman-dokumen/store', [PengirimanDokumenController::class, 'store'])->name('pengiriman-dokumen.store');
    Route::delete('/administrasi/pengiriman-dokumen/{id}', [PengirimanDokumenController::class, 'destroy'])->name('pengiriman-dokumen.destroy');
    Route::post('/pengiriman-dokumen/import', [PengirimanDokumenController::class, 'import'])->name('pengiriman-dokumen.import');
    Route::get('/pengiriman-dokumen/export/excel', [PengirimanDokumenController::class, 'exportExcel'])->name('pengiriman-dokumen.export.excel');
    Route::get('/pengiriman-dokumen/export/pdf', [PengirimanDokumenController::class, 'exportPdf'])->name('pengiriman-dokumen.export.pdf');
    
    // --- PERBAIKAN RUTE TEMPLATE PENGIRIMAN DOKUMEN ---
    // Letakkan ini DI DALAM middleware auth, dan pakai name route yg baru
    Route::get('/administrasi/pengiriman-dokumen/template-baru', [PengirimanDokumenController::class, 'downloadTemplate'])
        ->name('pengiriman-dokumen.template-baru');
        
    // (Route template lama tetap ada untuk modul lain)
    Route::get('/pengiriman-dokumen/template/excel', [PengirimanDokumenController::class, 'downloadTemplate'])->name('pengiriman-dokumen.template.excel');

    // ==========================================
    // MENU JASA FOTOCOPY (FIXED)
    // ==========================================
    Route::get('/administrasi/jasa-fotocopy', [App\Http\Controllers\JasaFotocopyController::class, 'index'])->name('administrasi.jasa-fotocopy');
    Route::post('/administrasi/jasa-fotocopy/store', [App\Http\Controllers\JasaFotocopyController::class, 'store'])->name('jasafotocopy.store');
    Route::put('/administrasi/jasa-fotocopy/{id}', [App\Http\Controllers\JasaFotocopyController::class, 'update'])->name('jasafotocopy.update');
    Route::delete('/administrasi/jasa-fotocopy/{id}', [App\Http\Controllers\JasaFotocopyController::class, 'destroy'])->name('jasafotocopy.destroy');
    
    // Fitur Tambahan
    Route::post('/administrasi/jasa-fotocopy/kolom-dinamis', [App\Http\Controllers\JasaFotocopyController::class, 'storeKolomDinamis'])->name('jasafotocopy.kolom.store');
    Route::delete('/administrasi/jasa-fotocopy/kolom-dinamis/{id}', [App\Http\Controllers\JasaFotocopyController::class, 'destroyKolomDinamis'])->name('jasafotocopy.kolom.destroy');
    Route::post('/administrasi/jasa-fotocopy/import', [App\Http\Controllers\JasaFotocopyController::class, 'importExcel'])->name('jasafotocopy.import');
    Route::get('/administrasi/jasa-fotocopy/export/excel', [App\Http\Controllers\JasaFotocopyController::class, 'exportExcel'])->name('jasafotocopy.export.excel');
    Route::get('/administrasi/jasa-fotocopy/export/pdf', [App\Http\Controllers\JasaFotocopyController::class, 'exportPdf'])->name('jasafotocopy.export.pdf');

    // REVISI RUTE SURAT MASUK & KELUAR (TERBARU)
    // ==========================================
    Route::group(['prefix' => 'administrasi/surat-masuk-keluar', 'as' => 'surat.'], function () {
        
        // 1. Tampilan Utama (Dashboard & Tabel)
        Route::get('/', [SuratController::class, 'index'])->name('index');
        
        // 2. Ekspor Laporan
        Route::get('/export/pdf', [SuratController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [SuratController::class, 'exportExcel'])->name('export.excel');
        
        // 3. Impor Data
        Route::post('/import/excel', [SuratController::class, 'importExcel'])->name('import.excel');
        
        // 4. Pengaturan Kolom Dinamis (Khusus Modul Surat)
        Route::post('/kolom-dinamis', [SuratController::class, 'storeKolomDinamis'])->name('kolom-dinamis.store');
        Route::delete('/kolom-dinamis/{id}', [SuratController::class, 'destroyKolomDinamis'])->name('kolom-dinamis.destroy');

        // 5. CRUD Data Satuan (Posisikan /{id} di bawah agar tidak bentrok dengan rute statis di atasnya)
        Route::post('/', [SuratController::class, 'store'])->name('store');
        Route::get('/{id}', [SuratController::class, 'show'])->name('show'); // Rute ke halaman detail baru
        Route::put('/{id}', [SuratController::class, 'update'])->name('update');
        Route::delete('/{id}', [SuratController::class, 'destroy'])->name('destroy');
        
    });
    
    // ==========================================
    // MENU BAR SK MEMO (UPDATED 1 TABEL + ANTI ERROR)
    // ==========================================
    Route::get('/administrasi/bar-sk-memo', [BarSkMemoController::class, 'index'])->name('bar-sk-memo.index');
    
    // Rute Tabel Terbit
    Route::post('/administrasi/bar-sk-memo/terbit', [BarSkMemoController::class, 'storeTerbit'])->name('bar-sk-memo.storeTerbit');
    Route::delete('/administrasi/bar-sk-memo/terbit/destroy', [BarSkMemoController::class, 'destroyTerbit'])->name('bar-sk-memo.destroyTerbit');
    
    // Tameng Anti-Error Terbit (Kalau kepencet refresh/akses URL langsung)
    Route::get('/administrasi/bar-sk-memo/terbit', function() { return redirect()->route('bar-sk-memo.index'); });
    Route::get('/administrasi/bar-sk-memo/terbit/destroy', function() { return redirect()->route('bar-sk-memo.index'); });
    
    // Rute Tabel Proses
    Route::post('/administrasi/bar-sk-memo/proses', [BarSkMemoController::class, 'storeProses'])->name('bar-sk-memo.storeProses');
    Route::delete('/administrasi/bar-sk-memo/proses/destroy', [BarSkMemoController::class, 'destroyProses'])->name('bar-sk-memo.destroyProses');

    // Tameng Anti-Error Proses (Kalau kepencet refresh/akses URL langsung)
    Route::get('/administrasi/bar-sk-memo/proses', function() { return redirect()->route('bar-sk-memo.index'); });
    Route::get('/administrasi/bar-sk-memo/proses/destroy', function() { return redirect()->route('bar-sk-memo.index'); });

    // Export, Import, dan Download Template BAR SK MEMO
    Route::get('/administrasi/bar-sk-memo/export/excel', [BarSkMemoController::class, 'exportExcel'])->name('bar-sk-memo.export.excel');
    Route::get('/administrasi/bar-sk-memo/export/pdf', [BarSkMemoController::class, 'exportPdf'])->name('bar-sk-memo.export.pdf');
    
    // RUTE BARU YANG HARUS DITAMBAHKAN UNTUK IMPORT & TEMPLATE
    Route::post('/administrasi/bar-sk-memo/import/terbit', [BarSkMemoController::class, 'importExcelTerbit'])->name('bar-sk-memo.import.terbit');
    Route::post('/administrasi/bar-sk-memo/import/proses', [BarSkMemoController::class, 'importExcelProses'])->name('bar-sk-memo.import.proses');
    Route::get('/administrasi/bar-sk-memo/template/terbit', [BarSkMemoController::class, 'downloadTemplateTerbit'])->name('bar-sk-memo.template.terbit');
    Route::get('/administrasi/bar-sk-memo/template/proses', [BarSkMemoController::class, 'downloadTemplateProses'])->name('bar-sk-memo.template.proses');
    // ==========================================
    // MENU PEMELIHARAAN (UPDATED)
    // ==========================================
    Route::get('/administrasi/pemeliharaan', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index');
    
    // Rutin
    Route::post('/administrasi/pemeliharaan/rutin/master', [PemeliharaanController::class, 'storeMasterRutin'])->name('pemeliharaan-rutin.master.store');
    Route::delete('/administrasi/pemeliharaan/rutin/master/{id}', [PemeliharaanController::class, 'destroyMasterRutin'])->name('pemeliharaan-rutin.master.destroy');
    Route::post('/administrasi/pemeliharaan/rutin', [PemeliharaanController::class, 'storeRutin'])->name('pemeliharaan-rutin.store');
    Route::post('/administrasi/pemeliharaan/rutin/bulan', [PemeliharaanController::class, 'updateRutinBulan'])->name('pemeliharaan-rutin.updateBulan');
    Route::delete('/administrasi/pemeliharaan/rutin/bulan', [PemeliharaanController::class, 'destroyRutinBulan'])->name('pemeliharaan-rutin.destroyBulan');
    Route::post('/administrasi/pemeliharaan/rutin/import', [PemeliharaanController::class, 'importRutin'])->name('pemeliharaan-rutin.import');
    Route::get('/administrasi/pemeliharaan/rutin/export/excel', [PemeliharaanController::class, 'exportExcelRutin'])->name('pemeliharaan-rutin.export.excel');
    Route::get('/administrasi/pemeliharaan/rutin/export/pdf', [PemeliharaanController::class, 'exportPdfRutin'])->name('pemeliharaan-rutin.export.pdf');

    // Peralatan
    Route::post('/administrasi/pemeliharaan/peralatan/master', [PemeliharaanController::class, 'storeMasterPeralatan'])->name('pemeliharaan-peralatan.master.store');
    Route::delete('/administrasi/pemeliharaan/peralatan/master/{id}', [PemeliharaanController::class, 'destroyMasterPeralatan'])->name('pemeliharaan-peralatan.master.destroy');
    Route::post('/administrasi/pemeliharaan/peralatan', [PemeliharaanController::class, 'storePeralatan'])->name('pemeliharaan-peralatan.store');
    Route::post('/administrasi/pemeliharaan/peralatan/bulan', [PemeliharaanController::class, 'updatePeralatanBulan'])->name('pemeliharaan-peralatan.updateBulan');
    Route::delete('/administrasi/pemeliharaan/peralatan/bulan', [PemeliharaanController::class, 'destroyPeralatanBulan'])->name('pemeliharaan-peralatan.destroyBulan');
    Route::post('/administrasi/pemeliharaan/peralatan/import', [PemeliharaanController::class, 'importPeralatan'])->name('pemeliharaan-peralatan.import');
    Route::get('/administrasi/pemeliharaan/peralatan/export/excel', [PemeliharaanController::class, 'exportExcelPeralatan'])->name('pemeliharaan-peralatan.export.excel');
    Route::get('/administrasi/pemeliharaan/peralatan/export/pdf', [PemeliharaanController::class, 'exportPdfPeralatan'])->name('pemeliharaan-peralatan.export.pdf');
    // Undangan
    Route::get('/administrasi/undangan', [UndanganController::class, 'index'])->name('undangan.index');
    Route::post('/administrasi/undangan', [UndanganController::class, 'store'])->name('undangan.store');
    Route::delete('/administrasi/undangan/{id}', [UndanganController::class, 'destroy'])->name('undangan.destroy');
    Route::post('/administrasi/undangan/import', [UndanganController::class, 'import'])->name('undangan.import');
    Route::get('/administrasi/undangan/export/excel', [UndanganController::class, 'exportExcel'])->name('undangan.export.excel');
    Route::get('/administrasi/undangan/export/pdf', [UndanganController::class, 'exportPdf'])->name('undangan.export.pdf');
    Route::get('/administrasi/undangan/template', [UndanganController::class, 'downloadTemplate'])->name('undangan.download.template');

    // ==========================================
    // MANAJEMEN PENGGUNA (ADMIN)
    // ==========================================
    Route::get('/admin/manajemen-pengguna', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/manajemen-pengguna/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/manajemen-pengguna', [UserController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/manajemen-pengguna/{user}/toggle', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');
    Route::patch('/admin/manajemen-pengguna/{user}/password', [UserController::class, 'changePassword'])->name('admin.users.password.update');
    Route::delete('/admin/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/log-audit', [AuditLogController::class, 'index'])->name('log-audit');

    Route::get('/admin/pengaturan-warna', function () {
        return view('pengaturan-warna');
    })->name('pengaturan-warna');
}); // <-- INI ADALAH PENUTUP DARI GROUP AUTH

// Letakkan route generik di LUAR group auth
Route::get('/import-progress/{uuid}', [\App\Http\Controllers\ProgressController::class, 'importProgress'])->name('import.progress')->middleware('auth');
Route::get('/download-template/{modul}', [TemplateController::class, 'download'])->name('template.download')->middleware('auth');