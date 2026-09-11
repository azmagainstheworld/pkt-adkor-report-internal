<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja Bulanan ADKOR</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 20px; }
        .page-break { page-break-after: always; }
        
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

    <!-- ================= HALAMAN 1: PROGRAM STRATEGIS ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">1. PROGRAM STRATEGIS</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Sasaran</th>
                <th>Program Strategis</th>
                <th>Program Kegiatan</th>
                <th>Target Waktu</th>
                <th>Realisasi (%)</th>
                <th>Progress Saat Ini</th>
                <th>Kendala</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($programStrategis as $ps)
            <tr>
                <td class="text-center">{{ $ps->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                <td>{{ $ps->sasaran ?? '-' }}</td>
                <td>{{ $ps->program_strategis ?? '-' }}</td>
                <td>{{ $ps->deskripsi_kegiatan ?? '-' }}</td>
                <td class="text-center">{{ $ps->target_waktu_start ?? '-' }}@if($ps->target_waktu_end ?? null) - {{ $ps->target_waktu_end }}@endif</td>
                <td class="text-center">{{ $ps->realisasi ?? '-' }}</td>
                <td>{{ $ps->progress_saat_ini ?? '-' }}</td>
                <td>{{ $ps->kendala ?? '-' }}</td>
                <td>{{ $ps->keterangan_tambahan ?? '-' }}</td>
                <td class="text-center">{{ $ps->status ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center">Tidak ada data program strategis.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 2: SUMBER DAYA MANUSIA ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">2. SUMBER DAYA MANUSIA</div>
    <div class="chart-container">
        @if($chartSdm ?? null) <div class="chart-box-large"><div class="chart-title">Komposisi Karyawan</div><img src="{{ $chartSdm }}" style="width: 100%;"></div> @endif
        @if($chartAbsensi ?? null) <div class="chart-box-large"><div class="chart-title">Ketidakhadiran</div><img src="{{ $chartAbsensi }}" style="width: 100%;"></div> @endif
    </div>
    <table>
        <thead><tr><th>No</th><th>Nama</th><th>NPK</th><th>Gol/Grade</th><th>MPP/PBP</th><th>Ket. Pensiun</th><th>Keterangan</th></tr></thead>
        <tbody>
            @forelse($karyawan as $index => $k)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $k->nama ?? '-' }}</td>
                <td class="text-center">{{ $k->npk ?? '-' }}</td>
                <td class="text-center">{{ $k->gol_grade ?? '-' }}</td>
                <td class="text-center">{{ $k->mpp_pbp ? \Carbon\Carbon::parse($k->mpp_pbp)->format('d-m-Y') : '-' }}</td>
                <td class="text-center">{{ $k->ket_pensiun ?? '-' }}</td>
                <td class="text-center">{{ $k->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">Tidak ada data karyawan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 3: ANGGARAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">3. ANGGARAN ADMINISTRASI KORPORAT</div>
    <div class="chart-container">
        @if($chartAnggaranDikelola ?? null) <div class="chart-box"><div class="chart-title">Anggaran Dikelola</div><img src="{{ $chartAnggaranDikelola }}" style="width: 100%;"></div> @endif
        @if($chartAnggaranRutin ?? null) <div class="chart-box"><div class="chart-title">Anggaran Rutin</div><img src="{{ $chartAnggaranRutin }}" style="width: 100%;"></div> @endif
        @if($chartAnggaranInvestasi ?? null) <div class="chart-box"><div class="chart-title">Anggaran Investasi</div><img src="{{ $chartAnggaranInvestasi }}" style="width: 100%;"></div> @endif
    </div>

    <!-- Tabel 1: Rincian Realisasi Penggunaan dan Sisa Anggaran -->
    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Rincian Realisasi Penggunaan dan Sisa Anggaran</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Detail</th>
                <th>Anggaran RKAP</th>
                <th>Komitmen</th>
                <th>Realisasi</th>
                <th>Realisasi + Komitmen</th>
                <th>% Realisasi+Komitmen</th>
                <th>Sisa Anggaran</th>
                <th>% Sisa Anggaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($anggaran as $ang)
            @php
                $angRealKomit = ($ang->realisasi ?? 0) + ($ang->komitmen ?? 0);
                $angSisa = ($ang->rkap ?? 0) - $angRealKomit;
                $angPercRK = ($ang->rkap ?? 0) > 0 ? round($angRealKomit / $ang->rkap * 100, 1) : 0;
                $angPercSisa = ($ang->rkap ?? 0) > 0 ? round($angSisa / $ang->rkap * 100, 1) : 0;
            @endphp
            <tr>
                <td class="text-center">{{ $ang->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $ang->bulan ?? $bulan }}</td>
                <td>{{ $ang->detail_anggaran ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($ang->rkap ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($ang->komitmen ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($ang->realisasi ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($angRealKomit, 0, ',', '.') }}</td>
                <td class="text-center">{{ $angPercRK }}%</td>
                <td class="text-right font-bold">Rp {{ number_format($angSisa, 0, ',', '.') }}</td>
                <td class="text-center">{{ $angPercSisa }}%</td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center">Tidak ada data anggaran.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tabel 2: Ringkasan Realisasi & Komitmen per Bulan -->
    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Ringkasan Realisasi & Komitmen per Bulan</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>% Realisasi & Komitmen</th>
                <th>% Sisa Anggaran</th>
                <th>Anggaran RKAP</th>
                <th>Komitmen</th>
                <th>Realisasi</th>
                <th>Realisasi + Komitmen</th>
                <th>Sisa Anggaran</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                <td class="text-center">{{ $percRealisasiKomitmen ?? 0 }}%</td>
                <td class="text-center">{{ $percSisaAnggaran ?? 0 }}%</td>
                <td class="text-right">Rp {{ number_format($totalRkap ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalKomitmen ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalRealisasi ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format(($totalKomitmen ?? 0) + ($totalRealisasi ?? 0), 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($totalSisa ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabel 3: Perbandingan Sisa Anggaran Antar Kategori -->
    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Perbandingan Sisa Anggaran Antar Kategori</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Keterangan</th>
                <th>Anggaran Dikelola</th>
                <th>Anggaran Rutin</th>
                <th>Anggaran Investasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                <td class="text-center">Sisa</td>
                <td class="text-right">Rp {{ number_format($sisaPerKategoriAnggaran['Dikelola'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sisaPerKategoriAnggaran['Rutin'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sisaPerKategoriAnggaran['Investasi'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 4: BAR SK MEMO ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">4. BAR SK MEMO (MONITORING SK DIREKSI)</div>
    <div class="chart-container">
        @if($imgBarSkMemoTerbit ?? null) <div class="chart-box-large"><div class="chart-title">BAR SK MEMO TERBIT</div><img src="{{ $imgBarSkMemoTerbit }}" style="width: 100%;"></div> @endif
        @if($imgBarSkMemoProses ?? null) <div class="chart-box-large"><div class="chart-title">BAR SK MEMO PROSES</div><img src="{{ $imgBarSkMemoProses }}" style="width: 100%;"></div> @endif
    </div>

    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Proses</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Proses SKD Kep. Bersama</th>
                <th>Proses SKD Non Ratifikasi</th>
                <th>Proses SKD Ratifikasi</th>
                <th>Proses Memo Direksi</th>
                <th>Proses BAR Monitoring</th>
                <th>Proses BAR Manajemen</th>
            </tr>
        </thead>
        <tbody>
            @if($barSkMemo)
            <tr>
                <td class="text-center">{{ $barSkMemo->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $barSkMemo->bulan ?? $bulan }}</td>
                <td class="text-center">{{ $barSkMemo->proses_skd_keputusan_bersama ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->proses_skd_non_ratifikasi ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->proses_skd_ratifikasi ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->proses_memo_direksi ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->proses_bar_monitoring ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->proses_bar_manajemen ?? 0 }}</td>
            </tr>
            @else
            <tr><td colspan="8" class="text-center">Tidak ada data proses BAR SK Memo.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Terbit</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>SKD Keputusan Bersama</th>
                <th>SKD Non Ratifikasi</th>
                <th>SKD Ratifikasi</th>
                <th>Memo Direksi</th>
                <th>BAR Monitoring</th>
                <th>BAR Manajemen</th>
            </tr>
        </thead>
        <tbody>
            @if($barSkMemo)
            <tr>
                <td class="text-center">{{ $barSkMemo->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $barSkMemo->bulan ?? $bulan }}</td>
                <td class="text-center">{{ $barSkMemo->skd_keputusan_bersama_terbit ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->skd_non_ratifikasi_terbit ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->skd_ratifikasi_terbit ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->memo_direksi_terbit ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->bar_monitoring_terbit ?? 0 }}</td>
                <td class="text-center">{{ $barSkMemo->bar_manajemen_terbit ?? 0 }}</td>
            </tr>
            @else
            <tr><td colspan="8" class="text-center">Tidak ada data terbit BAR SK Memo.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 5: SURAT ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">5. SURAT MASUK & KELUAR</div>
    @if($chartSurat ?? null) <div class="chart-container"><div class="chart-title">Grafik Surat</div><img src="{{ $chartSurat }}" style="width: 60%;"></div> @endif

    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Akumulasi Laporan Surat Masuk dan Keluar</div>
    <table>
        <thead><tr><th>Tahun</th><th>Bulan</th><th>Surat Masuk</th><th>Surat Keluar</th></tr></thead>
        <tbody>
            @forelse($suratRekapData as $rk)
            <tr>
                <td class="text-center">{{ $rk->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $rk->bulan ?? $bulan }}</td>
                <td class="text-center">{{ $rk->total_masuk ?? 0 }}</td>
                <td class="text-center">{{ $rk->total_keluar ?? 0 }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center">Tidak ada data rekap surat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Arsip Detail Surat Satuan</div>
    <table>
        <thead><tr><th>No</th><th>Tahun</th><th>Bulan</th><th>Nomor Surat</th><th>Tanggal Surat</th><th>Judul Surat</th><th>Status</th><th>Jenis Surat</th></tr></thead>
        <tbody>
            @forelse($surat as $index => $srt)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $srt->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $srt->bulan ?? $bulan }}</td>
                <td>{{ $srt->nomor_surat ?? '-' }}</td>
                <td class="text-center">{{ $srt->tanggal_surat ?? '-' }}</td>
                <td>{{ $srt->judul_surat ?? '-' }}</td>
                <td class="text-center font-bold">{{ $srt->status ?? '-' }}</td>
                <td class="text-center">{{ $srt->jenis_surat ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">Tidak ada data surat.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 6: UNDANGAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">6. UNDANGAN</div>
    @if($chartUndangan ?? null) <div class="chart-container"><div class="chart-title">Statistik Distribusi Undangan per Bulan</div><img src="{{ $chartUndangan }}" style="width: 60%;"></div> @endif

    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Rekapitulasi Undangan Intern & Ekstern</div>
    <table>
        <thead><tr><th>Tahun</th><th>Bulan</th><th>Undangan Intern</th><th>Undangan Ekstern</th></tr></thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                <td class="text-center">{{ $totalIntern ?? 0 }}</td>
                <td class="text-center">{{ $totalEkstern ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Rincian Agenda Undangan</div>
    <table>
        <thead><tr><th>No</th><th>Tahun</th><th>Bulan</th><th>Jenis Undangan</th><th>Agenda</th></tr></thead>
        <tbody>
            @forelse($detailUndangan as $index => $du)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $du->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $du->bulan ?? $bulan }}</td>
                <td class="text-center">{{ $du->jenis_undangan ?? '-' }}</td>
                <td>{{ $du->agenda ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada data rincian agenda undangan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 7: PENGIRIMAN DOKUMEN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">7. PENGIRIMAN DOKUMEN</div>
    @if($chartPengiriman ?? null) <div class="chart-container"><div class="chart-title">Grafik Volume Pengiriman</div><img src="{{ $chartPengiriman }}" style="width: 60%;"></div> @endif

    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Rincian Volume Dokumen</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Penerimaan Mailroom</th>
                <th>Pengiriman Dalam Negeri</th>
                <th>Pengiriman Luar Negeri</th>
                <th>Reg. Surat Masuk DOF</th>
            </tr>
        </thead>
        <tbody>
            @if($pengirimanDokumen ?? null)
            <tr>
                <td class="text-center">{{ $pengirimanDokumen->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $pengirimanDokumen->bulan ?? $bulan }}</td>
                <td class="text-center">{{ $pengirimanDokumen->penerimaan_mailroom ?? 0 }}</td>
                <td class="text-center">{{ $pengirimanDokumen->pengiriman_dalam_negeri ?? 0 }}</td>
                <td class="text-center">{{ $pengirimanDokumen->pengiriman_luar_negeri ?? 0 }}</td>
                <td class="text-center">{{ $pengirimanDokumen->registrasi_surat_masuk_dof ?? 0 }}</td>
            </tr>
            @else
            <tr><td colspan="6" class="text-center">Tidak ada data volume dokumen.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Rincian Total Ongkir Pengiriman Bulanan</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Total Ongkir Dalam Negeri</th>
                <th>Total Ongkir Luar Negeri</th>
            </tr>
        </thead>
        <tbody>
            @if($pengirimanOngkir ?? null)
            <tr>
                <td class="text-center">{{ $pengirimanOngkir->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $pengirimanOngkir->bulan ?? $bulan }}</td>
                <td class="text-right">Rp {{ number_format($pengirimanOngkir->ongkir_dalam_negeri ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($pengirimanOngkir->ongkir_luar_negeri ?? 0, 0, ',', '.') }}</td>
            </tr>
            @else
            <tr><td colspan="4" class="text-center">Tidak ada data ongkir pengiriman.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 8: JASA KURIR ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">8. PENGIRIMAN JASA KURIR</div>
    @if($chartKurir ?? null) <div class="chart-container"><div class="chart-title">Statistik Penggunaan Jasa Kurir</div><img src="{{ $chartKurir }}" style="width: 60%;"></div> @endif

    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Detail Pengiriman per Ekspedisi</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($jasaKurirMaster as $master)
                <th>{{ $master->nama_kurir }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @if($jasaKurirTableData ?? null)
            <tr>
                <td class="text-center">{{ $jasaKurirTableData['tahun'] ?? $tahun }}</td>
                <td class="text-center">{{ $jasaKurirTableData['bulan'] ?? $bulan }}</td>
                @foreach($jasaKurirMaster as $master)
                <td class="text-center">{{ $jasaKurirTableData['kurir_' . $master->id] ?? 0 }}</td>
                @endforeach
                <td class="text-center font-bold">{{ $jasaKurirTableData['total_semua'] ?? 0 }}</td>
            </tr>
            @else
            <tr><td colspan="{{ $jasaKurirMaster->count() + 3 }}" class="text-center">Tidak ada data jasa kurir.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 9: FOTOCOPY ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">9. PENYEDIAAN FOTOCOPY</div>
    @if($chartFotocopy ?? null) <div class="chart-container"><div class="chart-title">Statistik Pemakaian Jasa Fotocopy</div><img src="{{ $chartFotocopy }}" style="width: 60%;"></div> @endif

    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Rekapitulasi Utama (Otomatis)</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Mesin FC</th>
                <th>Jumlah Pemakaian Jasa Penyediaan Fotocopy</th>
                <th>Nilai Jasa Penyediaan Fotocopy</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jasaFotocopy['dataTable1'] as $dt1)
            <tr>
                <td class="text-center">{{ $dt1['tahun'] ?? $tahun }}</td>
                <td class="text-center">{{ $dt1['bulan'] ?? $bulan }}</td>
                <td class="text-center">{{ $dt1['mesin_fc'] ?? 0 }}</td>
                <td class="text-center">{{ number_format($dt1['jumlah_pemakaian'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($dt1['nilai_jasa'] ?? 0, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada data pemakaian fotocopy.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Rekapitulasi Pemakaian Mesin Fotocopy Biaya Fee & Sewa Bulan</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Unit Kerja</th>
                <th>Cost Centre</th>
                <th>Jlh Pemakaian (Bln Terkait)</th>
                <th>Jlh Pemakaian (s.d. Bln Terkait)</th>
                <th>Ket.</th>
                <th>Type Mesin</th>
                <th>Biaya Fee Bulan Terkait</th>
                <th>Biaya Fee s.d. Bulan Terkait</th>
                <th>Biaya Fee/Lbr</th>
                <th>Biaya Sewa/Bulan</th>
                <th>Biaya Jasa Sewa & Fee Bln Terkait</th>
                <th>Total Biaya Sewa & Fee s.d. Bln Terkait</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jasaFotocopy['dataTable2'] as $index => $dt2)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $dt2['tahun'] ?? $tahun }}</td>
                <td class="text-center">{{ $dt2['bulan'] ?? $bulan }}</td>
                <td>{{ $dt2['unit_kerja'] ?? '-' }}</td>
                <td class="text-center">{{ $dt2['cost_centre'] ?? '-' }}</td>
                <td class="text-center">{{ number_format($dt2['pemakaian_bln_ini'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($dt2['pemakaian_sd'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ $dt2['keterangan'] ?? '-' }}</td>
                <td class="text-center">{{ $dt2['tipe_mesin'] ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($dt2['fee_bln_ini'] ?? 0, 2, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($dt2['fee_sd'] ?? 0, 2, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($dt2['fee_per_lbr'] ?? 0, 2, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($dt2['sewa_bln_ini'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($dt2['total_bln_ini'] ?? 0, 2, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($dt2['total_sd'] ?? 0, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="15" class="text-center">Tidak ada data rekapitulasi mesin fotocopy.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 10: KEARSIPAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">10. KEARSIPAN (TEKNIK, NON TEKNIK, DOF)</div>
    @if($chartKearsipan ?? null) <div class="chart-container"><div class="chart-title">Grafik Kearsipan</div><img src="{{ $chartKearsipan }}" style="width: 60%;"></div> @endif

    <!-- 10a. PA NON TEKNIK (TEKSTUAL) -->
    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">PA Non Teknik (Tekstual)</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($paTekstual['tabel1'] as $pt)
                <th>{{ $pt->nama_dokumen ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if($paTekstual['tabel1']->count() > 0)
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($paTekstual['tabel1'] as $pt)
                <td class="text-center">{{ $pt->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
            @else
            <tr><td colspan="2" class="text-center">Tidak ada data PA Non Teknik (Tekstual).</td></tr>
            @endif
        </tbody>
    </table>
    @if($paTekstual['tabel2']->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($paTekstual['tabel2'] as $pt)
                <th>{{ $pt->nama_dokumen ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($paTekstual['tabel2'] as $pt)
                <td class="text-center">{{ $pt->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    @endif

    <!-- 10b. PA NON TEKNIK (NON TEKSTUAL) -->
    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">PA Non Teknik (Non Tekstual)</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($paNonTekstual as $pnt)
                <th>{{ $pnt->jenis ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if($paNonTekstual->count() > 0)
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($paNonTekstual as $pnt)
                <td class="text-center">{{ $pnt->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
            @else
            <tr><td colspan="2" class="text-center">Tidak ada data PA Non Teknik (Non Tekstual).</td></tr>
            @endif
        </tbody>
    </table>

    <!-- 10c. PA TEKNIK -->
    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">PA Teknik</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($paTeknik['tabel1'] as $ptk)
                <th>{{ $ptk->nama_kegiatan ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if($paTeknik['tabel1']->count() > 0)
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($paTeknik['tabel1'] as $ptk)
                <td class="text-center">{{ $ptk->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
            @else
            <tr><td colspan="2" class="text-center">Tidak ada data PA Teknik.</td></tr>
            @endif
        </tbody>
    </table>
    @if($paTeknik['tabel2']->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($paTeknik['tabel2'] as $ptk)
                <th>{{ $ptk->nama_kegiatan ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($paTeknik['tabel2'] as $ptk)
                <td class="text-center">{{ $ptk->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    @endif

    <!-- 10d. DIGITAL OFFICE (DOF) -->
    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Digital Office (DOF)</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($dof['tabel1'] as $df)
                <th>{{ $df->nama_kegiatan ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if($dof['tabel1']->count() > 0)
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($dof['tabel1'] as $df)
                <td class="text-center">{{ $df->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
            @else
            <tr><td colspan="2" class="text-center">Tidak ada data DOF.</td></tr>
            @endif
        </tbody>
    </table>
    @if($dof['tabel2']->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($dof['tabel2'] as $df)
                <th>{{ $df->nama_kegiatan ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($dof['tabel2'] as $df)
                <td class="text-center">{{ $df->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    @endif

    <div class="page-break"></div>

    <!-- ================= HALAMAN 11: PERIZINAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">11. PERIZINAN PERKANTORAN</div>
    @if($chartPerizinan ?? null) <div class="chart-container"><div class="chart-title">Statistik Perizinan Terbit (Semua Tahun)</div><img src="{{ $chartPerizinan }}" style="width: 60%;"></div> @endif

    <!-- Ringkasan Kegiatan Perizinan Terbit -->
    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Ringkasan Kegiatan Perizinan Terbit</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Produk</th>
                <th>Aset</th>
                <th>Proyek</th>
                <th>Peralatan Pabrik</th>
                <th>Adm & Lainnya</th>
                <th>Total Perizinan Terbit</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $ringkasanTerbit->tahun ?? $tahun }}</td>
                <td class="text-center">{{ $ringkasanTerbit->bulan ?? $bulan }}</td>
                <td class="text-center">{{ $ringkasanTerbit->produk ?? 0 }}</td>
                <td class="text-center">{{ $ringkasanTerbit->aset ?? 0 }}</td>
                <td class="text-center">{{ $ringkasanTerbit->proyek ?? 0 }}</td>
                <td class="text-center">{{ $ringkasanTerbit->peralatan_pabrik ?? 0 }}</td>
                <td class="text-center">{{ $ringkasanTerbit->adm ?? 0 }}</td>
                <td class="text-center font-bold">{{ $ringkasanTerbit->total_terbit ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Daftar Perizinan Terbit -->
    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Daftar Perizinan Terbit</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tahun</th>
                <th>Perizinan Terbit</th>
                <th>Nomor</th>
                <th>Terbit</th>
                <th>Berakhir</th>
                <th>Instansi Penerbit</th>
                <th>Bulan</th>
                <th>Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($perizinanTerbit as $index => $izin)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $tahun }}</td>
                <td>{{ $izin->nama_perizinan ?? '-' }}</td>
                <td class="text-center">{{ $izin->nomor ?? '-' }}</td>
                <td class="text-center">{{ $izin->tanggal_sejak ?? '-' }}</td>
                <td class="text-center">{{ $izin->tanggal_akhir ?? '-' }}</td>
                <td>{{ $izin->instansi_penerbit ?? '-' }}</td>
                <td class="text-center">{{ $bulan }}</td>
                <td class="text-center">{{ $izin->kegiatan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">Tidak ada data perizinan terbit.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 12: PELAPORAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">12. PELAPORAN KORPORASI</div>
    @if($chartPelaporan ?? null) <div class="chart-container"><div class="chart-title">Grafik Pelaporan</div><img src="{{ $chartPelaporan }}" style="width: 60%;"></div> @endif

    <!-- Ringkasan Akumulasi Laporan -->
    <div class="font-bold" style="color:#1E3A8A; margin-bottom:5px;">Ringkasan Akumulasi Laporan</div>
    @php
        $pelTotalEksternal = $pelaporan->where('tujuan', 'Eksternal')->count();
        $pelTotalInternal = $pelaporan->where('tujuan', 'Internal')->count();
    @endphp
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                <th>Tujuan Eksternal</th>
                <th>Tujuan Internal</th>
                <th>Total Laporan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                <td class="text-center">{{ $pelTotalEksternal }}</td>
                <td class="text-center">{{ $pelTotalInternal }}</td>
                <td class="text-center font-bold">{{ $pelTotalEksternal + $pelTotalInternal }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Daftar Rincian Pelaporan -->
    <div class="font-bold" style="color:#1E3A8A; margin:10px 0 5px;">Daftar Rincian Pelaporan</div>
    <table>
        <thead><tr><th>Tujuan Laporan</th><th>Nomor Laporan</th><th>Laporan</th><th>Tanggal</th><th>Jenis</th></tr></thead>
        <tbody>
            @forelse($pelaporan as $lap)
            <tr>
                <td class="text-center font-bold">{{ $lap->tujuan ?? '-' }}</td>
                <td class="text-center">{{ $lap->nomor ?? '-' }}</td>
                <td>{{ $lap->laporan ?? '-' }}</td>
                <td class="text-center">{{ $lap->tanggal ?? '-' }}</td>
                <td class="text-center">{{ $lap->jenis ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada data pelaporan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ================= HALAMAN 13: PEMELIHARAAN ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">13. PEMELIHARAAN PERALATAN & FURNITUR</div>
    @if($chartPemeliharaan ?? null) <div class="chart-container"><div class="chart-title">Grafik Pemeliharaan</div><img src="{{ $chartPemeliharaan }}" style="width: 60%;"></div> @endif
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($pemeliharaanRutin as $pr)
                <th>{{ $pr->nama_pemeliharaan ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if($pemeliharaanRutin->count() > 0)
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($pemeliharaanRutin as $pr)
                <td class="text-center">{{ $pr->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
            @else
            <tr><td colspan="2" class="text-center">Tidak ada data pemeliharaan rutin.</td></tr>
            @endif
        </tbody>
    </table>

    @if($pemeliharaanPeralatan->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Bulan</th>
                @foreach($pemeliharaanPeralatan as $pp)
                <th>{{ $pp->nama_peralatan ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $tahun }}</td>
                <td class="text-center">{{ $bulan }}</td>
                @foreach($pemeliharaanPeralatan as $pp)
                <td class="text-center">{{ $pp->jumlah ?? 0 }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    @endif

    <div class="page-break"></div>

    <!-- ================= HALAMAN 14: MASALAH DAN KENDALA (TANDA TANGAN DI SINI) ================= -->
    <div class="header-title">LAPORAN KINERJA BULANAN ADKOR</div>
    <div class="header-subtitle">Periode: {{ strtoupper($bulan) }} {{ $tahun }}</div>

    <div class="section-header">14. MASALAH & KENDALA OPERASIONAL</div>
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