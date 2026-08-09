<?php

use Illuminate\Support\Facades\Route;
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

    Route::get('/summary', [SummaryController::class, 'index'])->name('summary.index');
    // Manajemen Pengguna (Admin)
    Route::get('/admin/manajemen-pengguna', [UserController::class, 'index'])->name('admin.users.index');
    
    // Rute untuk Pendaftaran (Create/Store)
    Route::get('/admin/manajemen-pengguna/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/manajemen-pengguna', [UserController::class, 'store'])->name('admin.users.store');
    
    Route::patch('/admin/manajemen-pengguna/{user}/toggle', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');
    
    // Rute untuk Ubah Password
    // Route::patch('/admin/manajemen-pengguna/{user}/password', [UserController::class, 'changePassword'])->name('admin.users.password.update');
    
    Route::delete('/admin/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');



    // ==========================================
    // RUTE MODUL APLIKASI
    // ==========================================
    
    Route::get('/program-strategis', [\App\Http\Controllers\ProgramStrategisController::class, 'index'])->name('program-strategis.index');
    Route::post('/program-strategis', [\App\Http\Controllers\ProgramStrategisController::class, 'store'])->name('program-strategis.store');
    Route::put('/program-strategis/{id}', [\App\Http\Controllers\ProgramStrategisController::class, 'update'])->name('program-strategis.update');
    Route::delete('/program-strategis/{id}', [\App\Http\Controllers\ProgramStrategisController::class, 'destroy'])->name('program-strategis.destroy');

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

    // Anggaran Administrasi
    Route::get('/anggaran', [AnggaranController::class, 'index'])->name('anggaran.index');
    Route::post('/anggaran', [AnggaranController::class, 'store'])->name('anggaran.store');

    Route::get('/salinan-anggaran', function () {
        return view('salinan-anggaran');
    })->name('salinan-anggaran');

    // Perizinan Perkantoran
    Route::get('/perizinan-perkantoran', [PerizinanPerkantoranController::class, 'index'])->name('perizinan-perkantoran.index');
    Route::post('/perizinan-perkantoran/store', [PerizinanPerkantoranController::class, 'store'])->name('perizinan-perkantoran.store');
    Route::put('/perizinan-perkantoran/{id}', [PerizinanPerkantoranController::class, 'update'])->name('perizinan-perkantoran.update');
    Route::delete('/perizinan-perkantoran/{id}', [PerizinanPerkantoranController::class, 'destroy'])->name('perizinan-perkantoran.destroy');

    Route::post('/perizinan-proses-list', [PerizinanPerkantoranController::class, 'storeProses'])->name('perizinan-proses.store');
    Route::put('/perizinan-proses-list/{id}', [PerizinanPerkantoranController::class, 'updateProses'])->name('perizinan-proses.update');
    Route::delete('/perizinan-proses-list/{id}', [PerizinanPerkantoranController::class, 'destroyProses'])->name('perizinan-proses.destroy');

    // Menu Struktur Organisasi
    Route::get('/struktur-organisasi', [\App\Http\Controllers\StrukturOrganisasiController::class, 'index'])->name('struktur-organisasi.index');
    Route::post('/struktur-organisasi', [\App\Http\Controllers\StrukturOrganisasiController::class, 'update'])->name('struktur-organisasi.update');

    // Pelaporan
    Route::get('/pelaporan', [PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::post('/pelaporan', [PelaporanController::class, 'store'])->name('pelaporan.store');
    Route::put('/pelaporan/{id}', [PelaporanController::class, 'update'])->name('pelaporan.update');
    Route::delete('/pelaporan/{id}', [PelaporanController::class, 'destroy'])->name('pelaporan.destroy');

    Route::get('/masalah-kendala', [MasalahKendalaController::class, 'index'])->name('masalah-kendala.index');
    Route::post('/masalah-kendala', [MasalahKendalaController::class, 'store'])->name('masalah-kendala.store');
    Route::put('/masalah-kendala/{id}', [MasalahKendalaController::class, 'update'])->name('masalah-kendala.update');
    Route::delete('/masalah-kendala/{id}', [MasalahKendalaController::class, 'destroy'])->name('masalah-kendala.destroy');

    // --- Grup Kearsipan ---

    // Rute Redirect (Agar URL lama tidak error 404)
    Route::get('/kearsipan/pa-non-teknik', function () {
        return redirect()->route('pa-tekstual.index');
    });

    // Rute Baru
    Route::get('/kearsipan/pa-non-teknik/tekstual', [\App\Http\Controllers\PaTekstualController::class, 'index'])->name('pa-tekstual.index');
    Route::post('/kearsipan/pa-non-teknik/tekstual/dokumen', [\App\Http\Controllers\PaTekstualController::class, 'storeDokumen'])->name('pa-tekstual.storeDokumen');
    Route::post('/kearsipan/pa-non-teknik/tekstual/update-bulan', [\App\Http\Controllers\PaTekstualController::class, 'updateBulan'])->name('pa-tekstual.updateBulan');
    Route::delete('/kearsipan/pa-non-teknik/tekstual/destroy-bulan', [\App\Http\Controllers\PaTekstualController::class, 'destroyBulan'])->name('pa-tekstual.destroyBulan');
    
    Route::get('/kearsipan/pa-teknik', function () {
        return view('kearsipan-pa-teknik');
    })->name('kearsipan.pa-teknik');

    // ==========================================
    // MENU NON TEKNIK NON TEKSTUAL (ARSIP) - STRUKTUR DINAMIS
    // ==========================================

    Route::get('/non-teknik-non-tekstual', [\App\Http\Controllers\PaNonTekstualController::class, 'index'])->name('non-teknik-non-tekstual.index');

    // Rute Store, Update, Destroy (Mirip Tekstual)
    Route::post('/non-teknik-non-tekstual/dokumen', [\App\Http\Controllers\PaNonTekstualController::class, 'storeDokumen'])->name('non-teknik-non-tekstual.storeDokumen');
    Route::post('/non-teknik-non-tekstual/update-bulan', [\App\Http\Controllers\PaNonTekstualController::class, 'updateBulan'])->name('non-teknik-non-tekstual.updateBulan');
    Route::delete('/non-teknik-non-tekstual/destroy-bulan', [\App\Http\Controllers\PaNonTekstualController::class, 'destroyBulan'])->name('non-teknik-non-tekstual.destroyBulan');

    Route::get('/kearsipan/pa-teknik', [\App\Http\Controllers\PaTeknikController::class, 'index'])->name('pa-teknik.index');
    Route::post('/kearsipan/pa-teknik/dokumen', [\App\Http\Controllers\PaTeknikController::class, 'storeDokumen'])->name('pa-teknik.storeDokumen');
    Route::post('/kearsipan/pa-teknik/update-bulan', [\App\Http\Controllers\PaTeknikController::class, 'updateBulan'])->name('pa-teknik.updateBulan');
    Route::delete('/kearsipan/pa-teknik/destroy-bulan', [\App\Http\Controllers\PaTeknikController::class, 'destroyBulan'])->name('pa-teknik.destroyBulan');
    
    Route::get('/kearsipan/dof', [\App\Http\Controllers\DofController::class, 'index'])->name('dof.index');
    Route::post('/kearsipan/dof/dokumen', [\App\Http\Controllers\DofController::class, 'storeDokumen'])->name('dof.storeDokumen');
    Route::post('/kearsipan/dof/update-bulan', [\App\Http\Controllers\DofController::class, 'updateBulan'])->name('dof.updateBulan');
    Route::delete('/kearsipan/dof/destroy-bulan', [\App\Http\Controllers\DofController::class, 'destroyBulan'])->name('dof.destroyBulan');

    Route::get('/kearsipan/rekap', [\App\Http\Controllers\RekapController::class, 'index'])->name('rekap.index');
    Route::post('/kearsipan/rekap/data', [\App\Http\Controllers\RekapController::class, 'storeData'])->name('rekap.storeData');
    Route::post('/kearsipan/rekap/update-bulan', [\App\Http\Controllers\RekapController::class, 'updateBulan'])->name('rekap.updateBulan');
    Route::delete('/kearsipan/rekap/destroy-bulan', [\App\Http\Controllers\RekapController::class, 'destroyBulan'])->name('rekap.destroyBulan');

    // --- Grup Administrasi ---
    Route::get('/administrasi/jasa-kurir', [JasaKurirController::class, 'index'])->name('jasakurir');
    Route::post('/administrasi/jasa-kurir/master', [JasaKurirController::class, 'storeMaster'])->name('jasakurir.master.store');
    Route::post('/administrasi/jasa-kurir/data', [JasaKurirController::class, 'storeData'])->name('jasakurir.data.store');

    Route::get('/administrasi/pengiriman-dokumen', [PengirimanDokumenController::class, 'index'])->name('pengiriman-dokumen.index');
    Route::post('/administrasi/pengiriman-dokumen/store', [PengirimanDokumenController::class, 'store'])->name('pengiriman-dokumen.store');

    Route::get('/administrasi/jasa-fotocopy', function () {
        return view('jasa-fotocopy');
    })->name('administrasi.jasa-fotocopy');

    Route::get('/administrasi/surat-masuk-keluar', function () {
        return view('surat-masuk-keluar');
    })->name('administrasi.surat-masuk-keluar');

    // ==========================================
    // MENU BAR SK MEMO
    // ==========================================
    Route::get('/administrasi/bar-sk-memo', [BarSkMemoController::class, 'index'])->name('bar-sk-memo.index');

    // CRUD Data Dokumen Dinamis (Untuk Terbit & Proses)
    Route::post('/administrasi/bar-sk-memo/dokumen', [BarSkMemoController::class, 'storeDokumen'])->name('bar-sk-memo.storeDokumen');
    Route::post('/administrasi/bar-sk-memo/dokumen/update-bulan', [BarSkMemoController::class, 'updateBulan'])->name('bar-sk-memo.updateBulan');
    Route::delete('/administrasi/bar-sk-memo/dokumen/destroy-bulan', [BarSkMemoController::class, 'destroyBulan'])->name('bar-sk-memo.destroyBulan');

    // CRUD Data Rapat
    Route::post('/administrasi/bar-sk-memo/rapat', [BarSkMemoController::class, 'storeRapat'])->name('bar-sk-memo.storeRapat');
    Route::put('/administrasi/bar-sk-memo/rapat/{id}', [BarSkMemoController::class, 'updateRapat'])->name('bar-sk-memo.updateRapat');
    Route::delete('/administrasi/bar-sk-memo/rapat/{id}', [BarSkMemoController::class, 'destroyRapat'])->name('bar-sk-memo.destroyRapat');

    // CRUD SKD & Memo
    Route::post('/administrasi/bar-sk-memo/skd-terbit', [BarSkMemoController::class, 'storeSkdTerbit'])->name('skd-terbit.store');
    Route::put('/administrasi/bar-sk-memo/skd-terbit/{id}', [BarSkMemoController::class, 'updateSkdTerbit'])->name('skd-terbit.update');
    Route::delete('/administrasi/bar-sk-memo/skd-terbit/{id}', [BarSkMemoController::class, 'destroySkdTerbit'])->name('skd-terbit.destroy');

    Route::post('/administrasi/bar-sk-memo/skd-proses', [BarSkMemoController::class, 'storeSkdProses'])->name('skd-proses.store');
    Route::put('/administrasi/bar-sk-memo/skd-proses/{id}', [BarSkMemoController::class, 'updateSkdProses'])->name('skd-proses.update');
    Route::delete('/administrasi/bar-sk-memo/skd-proses/{id}', [BarSkMemoController::class, 'destroySkdProses'])->name('skd-proses.destroy');

    Route::post('/administrasi/bar-sk-memo/memo-terbit', [BarSkMemoController::class, 'storeMemoTerbit'])->name('memo-terbit.store');
    Route::put('/administrasi/bar-sk-memo/memo-terbit/{id}', [BarSkMemoController::class, 'updateMemoTerbit'])->name('memo-terbit.update');
    Route::delete('/administrasi/bar-sk-memo/memo-terbit/{id}', [BarSkMemoController::class, 'destroyMemoTerbit'])->name('memo-terbit.destroy');

    Route::post('/administrasi/bar-sk-memo/memo-proses', [BarSkMemoController::class, 'storeMemoProses'])->name('memo-proses.store');
    Route::put('/administrasi/bar-sk-memo/memo-proses/{id}', [BarSkMemoController::class, 'updateMemoProses'])->name('memo-proses.update');
    Route::delete('/administrasi/bar-sk-memo/memo-proses/{id}', [BarSkMemoController::class, 'destroyMemoProses'])->name('memo-proses.destroy');

    // ==========================================
    // MENU PEMELIHARAAN
    // ==========================================
    Route::get('/administrasi/pemeliharaan', [PemeliharaanController::class, 'index'])->name('pemeliharaan.index');

    // Route CRUD Tabel 1 (Rutin)
    Route::post('/administrasi/pemeliharaan/rutin', [\App\Http\Controllers\PemeliharaanController::class, 'storeRutin'])->name('pemeliharaan-rutin.store');
    Route::post('/administrasi/pemeliharaan/rutin/bulan', [\App\Http\Controllers\PemeliharaanController::class, 'updateRutinBulan'])->name('pemeliharaan-rutin.updateBulan');
    Route::delete('/administrasi/pemeliharaan/rutin/bulan', [\App\Http\Controllers\PemeliharaanController::class, 'destroyRutinBulan'])->name('pemeliharaan-rutin.destroyBulan');
    
    // Route CRUD Tabel 2 (Peralatan Dinamis)
    Route::post('/administrasi/pemeliharaan/peralatan', [PemeliharaanController::class, 'storePeralatan'])->name('pemeliharaan-peralatan.store');
    Route::post('/administrasi/pemeliharaan/peralatan/bulan', [PemeliharaanController::class, 'updatePeralatanBulan'])->name('pemeliharaan-peralatan.updateBulan');
    Route::delete('/administrasi/pemeliharaan/peralatan/bulan', [PemeliharaanController::class, 'destroyPeralatanBulan'])->name('pemeliharaan-peralatan.destroyBulan');

    // Rute untuk Undangan
    Route::get('/administrasi/undangan', [UndanganController::class, 'index'])->name('undangan.index');
    Route::post('/administrasi/undangan', [UndanganController::class, 'store'])->name('undangan.store');
    // ==========================================
    // MANAJEMEN PENGGUNA (ADMIN)
    // ==========================================
    // Menampilkan daftar pengguna (termasuk status online)
    Route::get('/admin/manajemen-pengguna', [UserController::class, 'index'])->name('admin.users.index');
    
    // Rute untuk Pendaftaran Pengguna Baru (Create/Store)
    Route::get('/admin/manajemen-pengguna/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/manajemen-pengguna', [UserController::class, 'store'])->name('admin.users.store');
    
    // Rute untuk Menonaktifkan/Mengaktifkan Akun (Toggle)
    Route::patch('/admin/manajemen-pengguna/{user}/toggle', [UserController::class, 'toggleStatus'])->name('admin.users.toggle');
    
    // Rute untuk Ubah Password Pengguna oleh Admin
    Route::patch('/admin/manajemen-pengguna/{user}/password', [UserController::class, 'changePassword'])->name('admin.users.password.update');
    
    // Rute untuk Menghapus Akun Pengguna Permanen
    Route::delete('/admin/manajemen-pengguna/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/log-audit', [AuditLogController::class, 'index'])->name('log-audit');

    Route::get('/admin/pengaturan-warna', function () {
        return view('pengaturan-warna');
    })->name('pengaturan-warna');
});
