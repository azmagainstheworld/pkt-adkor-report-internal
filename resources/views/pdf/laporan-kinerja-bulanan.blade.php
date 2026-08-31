<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Bulanan ADKOR</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 20px; }
        .page-break { page-break-after: always; }
        
        /* ================= COVER PAGE DESIGN (MIRIP GAMBAR REFERENSI) ================= */
        .cover-page { 
            position: relative;
            height: 95vh; 
            width: 100%;
            background-color: white;
            overflow: hidden;
            border: 4px solid #1E3A8A; /* Border luar biru */
        }
        
        /* Lengkungan Biru Atas Kiri */
        .shape-top-left {
            position: absolute;
            top: -50px;
            left: -50px;
            width: 300px;
            height: 300px;
            background-color: #1E3A8A;
            border-radius: 50%;
        }
        .shape-top-left-orange {
            position: absolute;
            top: -40px;
            left: -40px;
            width: 320px;
            height: 320px;
            border: 15px solid #F97316;
            border-radius: 50%;
            clip-path: polygon(0 0, 100% 0, 100% 50%, 0 50%);
            transform: rotate(-45deg);
        }

        /* Lengkungan Biru Bawah Kanan */
        .shape-bottom-right {
            position: absolute;
            bottom: -150px;
            right: -100px;
            width: 800px;
            height: 300px;
            background-color: #1E3A8A;
            border-top-left-radius: 500px;
            border-top-right-radius: 500px;
        }
        .shape-bottom-right-orange {
            position: absolute;
            bottom: 140px;
            right: -100px;
            width: 800px;
            height: 300px;
            border-top: 15px solid #F97316;
            border-top-left-radius: 500px;
            border-top-right-radius: 500px;
        }

        /* Lingkaran Logo Kanan */
        .circle-image {
            position: absolute;
            right: 40px;
            top: 40%;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            border: 8px solid #F97316;
            background-color: #eee;
            overflow: hidden;
            z-index: 10;
        }
        .circle-image-small {
            position: absolute;
            right: 40px;
            top: 30%;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid #F97316;
            background-color: #ddd;
            overflow: hidden;
            z-index: 11;
        }

        /* Teks Judul Cover */
        .cover-text-container {
            position: absolute;
            top: 15%;
            width: 100%;
            text-align: center;
            z-index: 5;
        }
        .cover-title-orange { font-size: 48px; font-weight: 900; color: #F97316; margin: 0; line-height: 1.1; }
        .cover-title-blue { font-size: 48px; font-weight: 900; color: #1E3A8A; margin: 0; line-height: 1.1; }

        /* Badge Bulan Tahun Kiri */
        .badge-container {
            position: absolute;
            left: 80px;
            bottom: 25%;
            text-align: center;
        }
        .badge-blue {
            background-color: #1E3A8A;
            color: white;
            padding: 10px 40px;
            border-radius: 30px;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .badge-year { font-size: 28px; font-weight: 900; color: #1E3A8A; }

        /* ================= KONTEN HALAMAN ================= */
        .header-title { font-size: 16px; font-weight: bold; color: #1E3A8A; margin-bottom: 2px; }
        .header-subtitle { font-size: 11px; margin-bottom: 15px; color: #555; }
        
        .section-header { background-color: #F97316; color: white; padding: 6px 10px; font-size: 11px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; vertical-align: middle; }
        th { background-color: #f3f4f6; font-weight: bold; text-align: center; color: #1E3A8A; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .chart-container { text-align: center; margin-bottom: 15px; width: 100%; }
        .chart-box { display: inline-block; width: 30%; text-align: center; margin: 0 1%; vertical-align: top; }
        .chart-box-large { display: inline-block; width: 45%; text-align: center; margin: 0 2%; vertical-align: top; }
        .chart-title { font-weight: bold; color: #F97316; margin-bottom: 5px; font-size: 10px; text-transform: uppercase; }
        
        .sign-area { margin-top: 40px; text-align: right; padding-right: 50px; page-break-inside: avoid; }
        .sign-name { font-weight: bold; text-decoration: underline; margin-top: 50px; margin-bottom: 3px; }
    </style>
</head>
<body>

    <!-- ================= COVER PAGE ================= -->
    <div class="cover-page">
        <!-- Bentuk Geometris -->
        <div class="shape-top-left"></div>
        <div class="shape-top-left-orange"></div>
        
        <div class="shape-bottom-right-orange"></div>
        <div class="shape-bottom-right"></div>
        
        <!-- Placeholder Lingkaran Kanan (Bisa diisi logo/gambar kantor PKT kalau kamu punya URL-nya) -->
        <div class="circle-image"></div>
        <div class="circle-image-small"></div>

        <!-- Teks Tengah -->
        <div class="cover-text-container">
            <h1 class="cover-title-orange">LAPORAN</h1>
            <h1 class="cover-title-blue">KINERJA</h1>
            <h1 class="cover-title-blue">BULANAN</h1>
        </div>

        <!-- Badge Bulan Kiri Bawah -->
        <div class="badge-container">
            <div style="font-size: 40px; color: #1E3A8A; margin-bottom: -10px;">❦</div> <!-- Simbol daun PKT -->
            <div class="badge-blue">{{ $bulan }}</div>
            <div class="badge-year">{{ $tahun }}</div>
        </div>
    </div>
    
    <div class="page-break"></div>

    <!-- ================= HALAMAN 1: PROGRAM STRATEGIS ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">1. PROGRAM STRATEGIS</div>
    <table>
        <thead>
            <tr><th style="width: 5%">No</th><th style="width: 25%">Program Strategis</th><th style="width: 30%">Deskripsi Kegiatan</th><th style="width: 15%">Target Waktu</th><th style="width: 25%">Realisasi</th></tr>
        </thead>
        <tbody>
            @forelse($programStrategis as $index => $ps)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{{ $ps->program_strategis ?? '-' }}</td><td>{{ $ps->deskripsi_kegiatan ?? '-' }}</td><td class="text-center">{{ $ps->target_waktu ?? '-' }}</td><td>{{ $ps->realisasi ?? '-' }}</td></tr>
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada data program strategis.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 2: SUMBER DAYA MANUSIA ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">2. SUMBER DAYA MANUSIA</div>
    <div class="chart-container">
        @if($chartSdm) <div class="chart-box-large"><div class="chart-title">Komposisi Karyawan</div><img src="{{ $chartSdm }}" style="width: 100%;"></div> @endif
        @if($chartAbsensi) <div class="chart-box-large"><div class="chart-title">Ketidakhadiran</div><img src="{{ $chartAbsensi }}" style="width: 100%;"></div> @endif
    </div>
    <table>
        <thead><tr><th>No</th><th>Nama Karyawan</th><th>NPK</th><th>Dinas</th><th>Cuti</th><th>Izin</th><th>Training</th><th>Dispensasi</th></tr></thead>
        <tbody>
            @forelse($ketidakhadiran as $index => $absen)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{{ $absen->nama ?? '-' }}</td><td class="text-center">{{ $absen->npk ?? '-' }}</td><td class="text-center">{{ $absen->dinas ?? 0 }}</td><td class="text-center">{{ $absen->cuti ?? 0 }}</td><td class="text-center">{{ $absen->izin ?? 0 }}</td><td class="text-center">{{ $absen->training ?? 0 }}</td><td class="text-center">{{ $absen->dispensasi ?? 0 }}</td></tr>
            @empty
            <tr><td colspan="8" class="text-center">Tidak ada data absensi.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 3: ANGGARAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">3. ANGGARAN ADMINISTRASI KORPORAT</div>
    <div class="chart-container">
        @if($chartAnggaranDikelola) <div class="chart-box"><div class="chart-title">Anggaran Dikelola</div><img src="{{ $chartAnggaranDikelola }}" style="width: 100%;"></div> @endif
        @if($chartAnggaranRutin) <div class="chart-box"><div class="chart-title">Anggaran Rutin</div><img src="{{ $chartAnggaranRutin }}" style="width: 100%;"></div> @endif
        @if($chartAnggaranInvestasi) <div class="chart-box"><div class="chart-title">Anggaran Investasi</div><img src="{{ $chartAnggaranInvestasi }}" style="width: 100%;"></div> @endif
    </div>
    <table>
        <thead><tr><th>Kategori</th><th>Detail Anggaran</th><th>RKAP</th><th>Realisasi</th><th>Komitmen</th><th>Sisa Anggaran</th></tr></thead>
        <tbody>
            @forelse($anggaran as $ang)
            <tr><td class="font-bold">{{ $ang->kategori ?? '-' }}</td><td>{{ $ang->detail_anggaran ?? '-' }}</td><td class="text-right">Rp {{ number_format($ang->rkap ?? 0, 0, ',', '.') }}</td><td class="text-right">Rp {{ number_format($ang->realisasi ?? 0, 0, ',', '.') }}</td><td class="text-right">Rp {{ number_format($ang->komitmen ?? 0, 0, ',', '.') }}</td><td class="text-right font-bold">Rp {{ number_format(($ang->rkap??0)-(($ang->realisasi??0)+($ang->komitmen??0)), 0, ',', '.') }}</td></tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data anggaran.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 4: BAR SK MEMO ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">4. BAR SK MEMO (MONITORING SK DIREKSI)</div>
    <div class="chart-container">
        @if($imgBarSkMemoTerbit) <div class="chart-box-large"><div class="chart-title">BAR SK MEMO TERBIT</div><img src="{{ $imgBarSkMemoTerbit }}" style="width: 100%;"></div> @endif
        @if($imgBarSkMemoProses) <div class="chart-box-large"><div class="chart-title">BAR SK MEMO PROSES</div><img src="{{ $imgBarSkMemoProses }}" style="width: 100%;"></div> @endif
    </div>
    <table>
        <thead><tr><th>Status</th><th>Bulan</th><th>SKD Kep. Bersama</th><th>SKD Non Ratif</th><th>SKD Ratif</th><th>Memo Direksi</th><th>BAR Monitor</th><th>BAR Manajemen</th></tr></thead>
        <tbody>
            <tr><td class="font-bold text-center">TERBIT</td><td class="text-center">{{ $bulan }}</td><td class="text-center">{{ $barSkMemo->skd_keputusan_bersama_terbit ?? 0 }}</td><td class="text-center">{{ $barSkMemo->skd_non_ratifikasi_terbit ?? 0 }}</td><td class="text-center">{{ $barSkMemo->skd_ratifikasi_terbit ?? 0 }}</td><td class="text-center">{{ $barSkMemo->memo_direksi_terbit ?? 0 }}</td><td class="text-center">{{ $barSkMemo->bar_monitoring_terbit ?? 0 }}</td><td class="text-center">{{ $barSkMemo->bar_manajemen_terbit ?? 0 }}</td></tr>
            <tr><td class="font-bold text-center">PROSES</td><td class="text-center">{{ $bulan }}</td><td class="text-center">{{ $barSkMemo->proses_skd_keputusan_bersama ?? 0 }}</td><td class="text-center">{{ $barSkMemo->proses_skd_non_ratifikasi ?? 0 }}</td><td class="text-center">{{ $barSkMemo->proses_skd_ratifikasi ?? 0 }}</td><td class="text-center">{{ $barSkMemo->proses_memo_direksi ?? 0 }}</td><td class="text-center">{{ $barSkMemo->proses_bar_monitoring ?? 0 }}</td><td class="text-center">{{ $barSkMemo->proses_bar_manajemen ?? 0 }}</td></tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 5: SURAT ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">5. SURAT MASUK & KELUAR</div>
    @if($chartSurat) <div class="chart-container"><div class="chart-title">Grafik Surat</div><img src="{{ $chartSurat }}" style="width: 60%;"></div> @endif
    <table>
        <thead><tr><th>No</th><th>Tanggal</th><th>Nomor Surat</th><th>Judul Surat</th><th>Jenis Surat</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($surat as $index => $srt)
            <tr><td class="text-center">{{ $index + 1 }}</td><td class="text-center">{{ $srt->tanggal_surat ?? '-' }}</td><td>{{ $srt->nomor_surat ?? '-' }}</td><td>{{ $srt->judul_surat ?? '-' }}</td><td class="text-center">{{ $srt->jenis_surat ?? '-' }}</td><td class="text-center font-bold">{{ $srt->status ?? '-' }}</td></tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data surat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 6: UNDANGAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">6. UNDANGAN</div>
    @if($chartUndangan) <div class="chart-container"><div class="chart-title">Grafik Undangan</div><img src="{{ $chartUndangan }}" style="width: 60%;"></div> @endif
    <table>
        <thead><tr><th>No</th><th>Bulan</th><th>Undangan Internal</th><th>Undangan Eksternal</th></tr></thead>
        <tbody>
            <tr><td class="text-center">1</td><td class="text-center">{{ $bulan }}</td><td class="text-center">{{ $undangan->where('jenis', 'Internal')->count() }}</td><td class="text-center">{{ $undangan->where('jenis', 'Eksternal')->count() }}</td></tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 7: JASA KURIR ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">7. PENGIRIMAN JASA KURIR</div>
    @if($chartKurir) <div class="chart-container"><div class="chart-title">Grafik Jasa Kurir</div><img src="{{ $chartKurir }}" style="width: 60%;"></div> @endif
    <table>
        <thead><tr><th>No</th><th>Nama Vendor Kurir</th><th>Bulan</th><th>Jumlah Pengiriman</th></tr></thead>
        <tbody>
            @forelse($jasaKurir as $index => $kurir)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{{ $kurir->nama_kurir ?? '-' }}</td><td class="text-center">{{ $kurir->bulan ?? '-' }}</td><td class="text-center">{{ $kurir->jumlah ?? 0 }}</td></tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada data jasa kurir.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 8: FOTOCOPY ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">8. PENYEDIAAN FOTOCOPY</div>
    @if($chartFotocopy) <div class="chart-container"><div class="chart-title">Grafik Pemakaian Fotocopy</div><img src="{{ $chartFotocopy }}" style="width: 60%;"></div> @endif
    <table>
        <thead><tr><th>No</th><th>Unit Kerja</th><th>Cost Centre</th><th>Tipe Mesin</th><th>Pemakaian (Lembar)</th><th>Total Biaya (Rp)</th></tr></thead>
        <tbody>
            @php $grandTotalLembar = 0; $grandTotalBiaya = 0; @endphp
            @forelse($fotocopy as $index => $fc)
            @php $totalBiaya = ($fc->pemakaian_lembar * $fc->biaya_fee_per_lembar) + $fc->biaya_sewa_mesin; $grandTotalLembar += $fc->pemakaian_lembar; $grandTotalBiaya += $totalBiaya; @endphp
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{{ $fc->unit_kerja ?? '-' }}</td><td class="text-center">{{ $fc->cost_centre ?? '-' }}</td><td class="text-center">{{ $fc->tipe_mesin ?? '-' }}</td><td class="text-center">{{ number_format($fc->pemakaian_lembar ?? 0, 0, ',', '.') }}</td><td class="text-right">Rp {{ number_format($totalBiaya, 2, ',', '.') }}</td></tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data penggunaan fotocopy.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 9: KEARSIPAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">9. KEARSIPAN (TEKNIK, NON TEKNIK, DOF)</div>
    @if($chartKearsipan) <div class="chart-container"><div class="chart-title">Grafik Kearsipan</div><img src="{{ $chartKearsipan }}" style="width: 60%;"></div> @endif
    <table>
        <thead><tr><th>No</th><th>Kategori Kearsipan</th><th>Nama Dokumen / Jenis</th><th>Jumlah</th></tr></thead>
        <tbody>
            @foreach($paTekstual as $index => $pt)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>PA Tekstual</td><td>{{ $pt->nama_dokumen ?? '-' }}</td><td class="text-center">{{ $pt->jumlah ?? 0 }}</td></tr>
            @endforeach
            @foreach($paNonTekstual as $index => $pnt)
            <tr><td class="text-center">{{ $index + count($paTekstual) + 1 }}</td><td>PA Non Tekstual</td><td>{{ $pnt->jenis ?? '-' }}</td><td class="text-center">{{ $pnt->jumlah ?? 0 }}</td></tr>
            @endforeach
            @foreach($paTeknik as $index => $ptk)
            <tr><td class="text-center">{{ $index + count($paTekstual) + count($paNonTekstual) + 1 }}</td><td>PA Teknik</td><td>{{ $ptk->nama_kegiatan ?? '-' }}</td><td class="text-center">{{ $ptk->jumlah ?? 0 }}</td></tr>
            @endforeach
            @foreach($dof as $index => $df)
            <tr><td class="text-center">{{ $index + count($paTekstual) + count($paNonTekstual) + count($paTeknik) + 1 }}</td><td>Digital Office (DOF)</td><td>{{ $df->nama_kegiatan ?? '-' }}</td><td class="text-center">{{ $df->jumlah ?? 0 }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 10: PERIZINAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">10. PERIZINAN PERKANTORAN</div>
    @if($chartPerizinan) <div class="chart-container"><div class="chart-title">Grafik Perizinan</div><img src="{{ $chartPerizinan }}" style="width: 60%;"></div> @endif
    <table>
        <thead><tr><th>No</th><th>Nomor Izin</th><th>Kegiatan</th><th>Instansi Penerbit</th><th>Berlaku</th><th>Berakhir</th></tr></thead>
        <tbody>
            @forelse($perizinan as $index => $izin)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{{ $izin->nomor ?? '-' }}</td><td>{{ $izin->kegiatan ?? '-' }}</td><td>{{ $izin->instansi_penerbit ?? '-' }}</td><td class="text-center">{{ $izin->tanggal_sejak ?? '-' }}</td><td class="text-center">{{ $izin->tanggal_akhir ?? '-' }}</td></tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data perizinan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 11: PELAPORAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">11. PELAPORAN KORPORASI</div>
    @if($chartPelaporan) <div class="chart-container"><div class="chart-title">Grafik Pelaporan</div><img src="{{ $chartPelaporan }}" style="width: 60%;"></div> @endif
    <table>
        <thead><tr><th>No</th><th>Nomor Laporan</th><th>Tujuan</th><th>Nama Laporan</th><th>Tanggal</th><th>Jenis</th></tr></thead>
        <tbody>
            @forelse($pelaporan as $index => $lap)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{{ $lap->nomor ?? '-' }}</td><td class="text-center font-bold">{{ $lap->tujuan ?? '-' }}</td><td>{{ $lap->laporan ?? '-' }}</td><td class="text-center">{{ $lap->tanggal ?? '-' }}</td><td class="text-center">{{ $lap->jenis ?? '-' }}</td></tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data pelaporan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 12: PEMELIHARAAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">12. PEMELIHARAAN PERALATAN & FURNITUR</div>
    @if($chartPemeliharaan) <div class="chart-container"><div class="chart-title">Grafik Pemeliharaan</div><img src="{{ $chartPemeliharaan }}" style="width: 60%;"></div> @endif
    <table>
        <thead><tr><th>No</th><th>Kategori Pemeliharaan</th><th>Nama Item / Kegiatan</th><th>Jumlah</th></tr></thead>
        <tbody>
            @foreach($pemeliharaanRutin as $index => $pr)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>Pemeliharaan Rutin</td><td>{{ $pr->nama_pemeliharaan ?? '-' }}</td><td class="text-center">{{ $pr->jumlah ?? 0 }}</td></tr>
            @endforeach
            @foreach($pemeliharaanPeralatan as $index => $pp)
            <tr><td class="text-center">{{ $index + count($pemeliharaanRutin) + 1 }}</td><td>Peralatan Kantor</td><td>{{ $pp->nama_peralatan ?? '-' }}</td><td class="text-center">{{ $pp->jumlah ?? 0 }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 13: MASALAH DAN KENDALA (TANDA TANGAN DI SINI) ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">13. MASALAH & KENDALA OPERASIONAL</div>
    <table>
        <thead><tr><th style="width: 5%;">No</th><th style="width: 45%;">Masalah / Kendala</th><th style="width: 50%;">Tindak Lanjut / Solusi</th></tr></thead>
        <tbody>
            @forelse($kendala as $index => $k)
            <tr><td class="text-center">{{ $index + 1 }}</td><td>{!! nl2br(e($k->masalah_kendala ?? '-')) !!}</td><td>{!! nl2br(e($k->solusi ?? '-')) !!}</td></tr>
            @empty
            <tr><td colspan="3" class="text-center">Tidak ada kendala/masalah yang dilaporkan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN (HANYA NAMA VP) -->
    <div class="sign-area">
        <p>Bontang, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
        <p>VP Administrasi Korporat,</p>
        <div class="sign-name">{{ $namaVp }}</div>
    </div>

</body>
</html>
