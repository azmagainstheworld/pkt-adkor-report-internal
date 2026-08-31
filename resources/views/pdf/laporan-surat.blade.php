<!DOCTYPE html>
<html>
<head>
    <title>Laporan Surat {{ $tahunFilter }} {{ $bulanFilter }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; line-height: 1.3; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; color: #0056A3; }
        .header p { margin: 2px 0; font-size: 11px; }
        
        .section-title { font-weight: bold; font-size: 13px; margin-top: 15px; margin-bottom: 8px; color: #444; border-left: 4px solid #F7941E; padding-left: 8px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        th, td { border: 1px solid #ccc; padding: 5px; word-wrap: break-word; vertical-align: top;}
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase;}
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Warna Jenis */
        .masuk { color: #0056A3; font-weight: bold; }
        .keluar { color: #F7941E; font-weight: bold; }
        
        /* Badge Status */
        .status-terkirim { color: #047481; background-color: #e6fffa; border: 1px solid #b2f5ea; padding: 2px 5px; rounded: 3px; font-size: 8px; font-weight: bold;}
        .status-batal { color: #c53030; background-color: #fff5f5; border: 1px solid #feb2b2; padding: 2px 5px; rounded: 3px; font-size: 8px; font-weight: bold;}

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN KINERJA PERSURATAN</h2>
        <p>Unit Kerja: Departemen Administrasi & Koordinasi</p>
        <p>Periode: <strong>{{ $tahunFilter == 'semua' ? 'Keseluruhan' : 'Tahun ' . $tahunFilter }} / Bulan: {{ $bulanFilter }}</strong></p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WiB</p>
    </div>

    <!-- ================= Req 3: TABEL 1 (REKAPITULASI - READ ONLY) ================= -->
    <div class="section-title">I. Tabel Akumulasi Laporan Bulanan (Hanya Status Terkirim)Saya mengerti sepenuhnya. Anda ingin mempertahankan **URL/Path lama** Anda agar tidak merubah link yang sudah tersebar di view atau modul lain, namun ingin menerapkan fungsi-fungsi baru ke dalam Controller tersebut.

Berikut adalah **perbaikan final** untuk `SuratController.php` dan View Anda. Saya telah menyesuaikan fungsi Controller agar menangani data transaksional (satuan) dan sinkron dengan **URL standar Laravel** yang kemungkinan besar Anda gunakan di routes lama Anda.

### Checklist Penyesuaian

*   [cite: 1] **Model `Surat.php`**: Sudah dipastikan menangani *casting* tanggal dan array kolom dinamis.
*   [ ] **Controller `SuratController.php`**: Diperbaiki total. Fungsi CRUD (`index`, `store`, `update`, `destroy`) sekarang menangani data satuan (transaksional), sinkron dengan URL standar, dan mendukung Impor/Ekspor Package baru.
*   [ ] **View `surat-masuk-keluar.blade.php`**: View total dirombak untuk menampilkan Chart tren, Tabel Rekap Bulanan (Read-Only), dan Tabel Detail Satuan (CRUD). Form Tambah/Edit disesuaikan untuk input satuan.
*   [ ] **Template PDF**: Disiapkan untuk menampilkan kedua tabel sesuai Req 2.

---

### Langkah 1: Perbaikan Model (`app/Models/Surat.php`)

Pondasi utama untuk casting data dinamis.

```php
<?php

namespace App\Models;

use App\Traits\Auditable; // Trait auditable Anda
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use Auditable;

    protected $auditModuleKey = 'surat';
    protected $table = 'surat';
    
    // guarded id agar kolom lain otomatis bisa diisi massal
    protected $guarded = ['id'];

    // Casting array untuk kolom tambahan dinamis dan date (Req 5 & 1)
    protected $casts = [
        'data_tambahan' => 'array',
        'tanggal_surat' => 'date', // Penting untuk formatting d/m/Y di View
    ];
}
