<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;

class CetakLaporanController extends Controller
{
    public function index()
    {
        return view('cetak-laporan');
    }

    public function cetakPDF(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'nama_vp' => 'required|string'
        ]);

        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $namaVp = $request->input('nama_vp');

        $bulanMap = ['Januari'=>'01','Februari'=>'02','Maret'=>'03','April'=>'04','Mei'=>'05','Juni'=>'06','Juli'=>'07','Agustus'=>'08','September'=>'09','Oktober'=>'10','November'=>'11','Desember'=>'12'];
        $bulanNum = $bulanMap[$bulan] ?? '01';

        $dataLaporan = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'namaVp' => $namaVp
        ];

        // ==========================================
        // 1. AMBIL DATA DARI SETIAP CONTROLLER (SINGLE SOURCE OF TRUTH)
        // ==========================================

        // 1. Program Strategis
        $psData = ProgramStrategisController::getReportData($tahun, $bulan);
        $dataLaporan['programStrategis'] = $psData['rawData'];

        // 2. Karyawan (SDM)
        $karyawanData = KaryawanController::getReportData($tahun, $bulan);
        $karyawan = $karyawanData['karyawan'];
        $dataLaporan['karyawan'] = $karyawan;
        $dataLaporan['countOrganik'] = $karyawanData['countOrganik'];
        $dataLaporan['countNonOrganik'] = $karyawanData['countNonOrganik'];

        // 3. Ketidakhadiran
        $absenData = KetidakhadiranController::getReportData($tahun, $bulan);
        $ketidakhadiran = $absenData['ketidakhadiran'];
        $dataLaporan['ketidakhadiran'] = $ketidakhadiran;
        $dataLaporan['totalPerKategoriAbsen'] = $absenData['totalPerKategori'];

        // 4. Anggaran
        $anggaranData = AnggaranController::getReportData($tahun, $bulan);
        $anggaran = $anggaranData['anggaran'];
        $dataLaporan['anggaran'] = $anggaran;
        $dataLaporan['anggaranPerKategori'] = $anggaranData['perKategori'];
        $dataLaporan['anggaranKategoriList'] = $anggaranData['kategoriList'];
        $dataLaporan['totalRkap'] = $anggaranData['totalRkap'];
        $dataLaporan['totalRealisasi'] = $anggaranData['totalRealisasi'];
        $dataLaporan['totalKomitmen'] = $anggaranData['totalKomitmen'];
        $dataLaporan['totalSisa'] = $anggaranData['totalSisa'];
        $dataLaporan['percRealisasiKomitmen'] = $anggaranData['percRealisasiKomitmen'];
        $dataLaporan['percSisaAnggaran'] = $anggaranData['percSisaAnggaran'];
        $dataLaporan['sisaPerKategoriAnggaran'] = $anggaranData['sisaPerKategori'];

        // 5. BAR SK Memo
        $barData = BarSkMemoController::getReportData($tahun, $bulan);
        $barSkMemo = $barData['barSkMemo'];
        $dataLaporan['barSkMemo'] = $barSkMemo;

        // 6. Surat Masuk & Keluar
        $suratData = SuratController::getReportData($tahun, $bulan);
        $dataLaporan['suratRekapData'] = $suratData['rekapData'];
        $dataLaporan['surat'] = $suratData['detailData'];

        // 7. Undangan
        $undanganData = UndanganController::getReportData($tahun, $bulan);
        $dataLaporan['undangan'] = $undanganData['undangan'];
        $dataLaporan['totalIntern'] = $undanganData['totalIntern'];
        $dataLaporan['totalEkstern'] = $undanganData['totalEkstern'];
        $dataLaporan['detailUndangan'] = $undanganData['detailUndangan'];

        // 8. Pengiriman Dokumen
        $pengirimanData = PengirimanDokumenController::getReportData($tahun, $bulan);
        $pengirimanDokumen = $pengirimanData['pengirimanVolume'];
        $dataLaporan['pengirimanDokumen'] = $pengirimanDokumen;
        $dataLaporan['pengirimanOngkir'] = $pengirimanData['pengirimanOngkir'];

        // 9. Jasa Kurir
        $kurirData = JasaKurirController::getReportData($tahun, $bulan);
        $jasaKurirMaster = $kurirData['kurirMaster'];
        $jasaKurirRaw = $kurirData['jasaKurirData'];
        $dataLaporan['jasaKurirMaster'] = $jasaKurirMaster;
        $dataLaporan['jasaKurirData'] = $jasaKurirRaw;
        $dataLaporan['jasaKurirTableData'] = collect($kurirData['tableData'])->first();

        // 10. Jasa Fotocopy
        $fotocopyData = JasaFotocopyController::getReportData($tahun, $bulan);
        $dataLaporan['jasaFotocopy'] = $fotocopyData;
        $mapBulanNumFc = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];
        $bulanNumFc = $mapBulanNumFc[$bulan] ?? null;
        $jasaFotocopySemua = \App\Models\JasaFotocopy::where('tahun', $tahun)
            ->where(function($q) use ($bulan, $bulanNumFc) {
                $q->whereRaw('LOWER(TRIM(bulan)) = ?', [strtolower(trim($bulan))]);
                if ($bulanNumFc) {
                    $q->orWhereRaw('CAST(bulan AS UNSIGNED) = ?', [$bulanNumFc]);
                }
            })
            ->get();
        $dataLaporan['jasaFotocopySemua'] = $jasaFotocopySemua;

        // 11. Perizinan Perkantoran
        $perizinanData = PerizinanPerkantoranController::getReportData($tahun, $bulan);
        $perizinanTerbit = $perizinanData['perizinanTerbit'];
        $dataLaporan['perizinanTerbit'] = $perizinanTerbit;
        $dataLaporan['perizinanProses'] = $perizinanData['perizinanProses'];
        $dataLaporan['ringkasanTerbit'] = $perizinanData['ringkasanTerbit'];
        $dataLaporan['statistikTerbitSemuaTahun'] = $perizinanData['statistikTerbitSemuaTahun'];

        // 12. Pelaporan
        $pelaporanData = PelaporanController::getReportData($tahun, $bulan);
        $pelaporan = $pelaporanData['pelaporan'];
        $dataLaporan['pelaporan'] = $pelaporan;

        // 13. Pemeliharaan
        $pemeliharaanData = PemeliharaanController::getReportData($tahun, $bulan);
        $dataLaporan['pemeliharaanRutin'] = $pemeliharaanData['pemeliharaanRutin'];
        $dataLaporan['pemeliharaanPeralatan'] = $pemeliharaanData['pemeliharaanPeralatan'];

        // 14. Kearsipan (PA Tekstual, Non Tekstual, Teknik, DOF)
        $paTekstualData = PaTekstualController::getReportData($tahun, $bulan);
        $dataLaporan['paTekstual'] = $paTekstualData['paTekstual'];

        $paNonTekstualData = PaNonTekstualController::getReportData($tahun, $bulan);
        $dataLaporan['paNonTekstual'] = $paNonTekstualData['paNonTekstual'];

        $paTeknikData = PaTeknikController::getReportData($tahun, $bulan);
        $dataLaporan['paTeknik'] = $paTeknikData['paTeknik'];

        $dofData = DofController::getReportData($tahun, $bulan);
        $dataLaporan['dof'] = $dofData['dof'];

        // 15. Masalah & Kendala
        $kendalaData = MasalahKendalaController::getReportData($tahun, $bulan);
        $dataLaporan['kendala'] = $kendalaData['kendala'];


        // ==========================================
        // 2. GENERATE GRAFIK BASE64 VIA QUICKCHART
        // ==========================================
        $generateChart = function($type, $labels, $datasets, $w = 500, $h = 250) {
            $config = ['type' => $type, 'data' => ['labels' => $labels, 'datasets' => $datasets], 'options' => ['legend' => ['position' => 'bottom'], 'plugins' => ['datalabels' => ['color' => '#ffffff', 'font' => ['weight' => 'bold']]]]];
            if($type === 'bar') $config['options']['scales'] = ['xAxes' => [['stacked' => true]], 'yAxes' => [['stacked' => true]]];
            
            $url = 'https://quickchart.io/chart?c=' . urlencode(json_encode($config)) . '&w=' . $w . '&h=' . $h;
            
            try {
                $response = Http::withoutVerifying()->timeout(15)->get($url);
                if ($response->successful()) { return 'data:image/png;base64,' . base64_encode($response->body()); }
            } catch (\Exception $e) {}
            return null;
        };

        // SDM Charts
        $org = $karyawanData['countOrganik'];
        $nonOrg = $karyawanData['countNonOrganik'];
        $dataLaporan['chartSdm'] = ($org > 0 || $nonOrg > 0) ? $generateChart('pie', ['Organik', 'Non Organik'], [['data' => [$org, $nonOrg], 'backgroundColor' => ['#F97316', '#1E3A8A']]], 300, 300) : null;

        // Pensiun Chart (from karyawanData)
        $pensiunCounts = $karyawanData['chartPensiunData'];
        if (array_sum($pensiunCounts) > 0) {
            $dataLaporan['chartPensiun'] = $generateChart('pie', array_keys($pensiunCounts), [['data' => array_values($pensiunCounts), 'backgroundColor' => ['#ef4444', '#f97316', '#eab308', '#22c55e']]], 300, 300);
        } else { $dataLaporan['chartPensiun'] = null; }

        // Absensi Chart
        $absenTotals = $absenData['totalPerKategori'];
        $dinas = $absenTotals['Dinas']; $cuti = $absenTotals['Cuti']; $izin = $absenTotals['Izin'];
        $training = $absenTotals['Training']; $dispensasi = $absenTotals['Dispensasi']; $detasering = $absenTotals['Detasering'];
        $dataLaporan['chartAbsensi'] = ($dinas > 0 || $cuti > 0 || $izin > 0 || $training > 0 || $dispensasi > 0 || $detasering > 0) 
            ? $generateChart('pie', ['Dinas', 'Cuti', 'Izin', 'Training', 'Dispensasi', 'Detasering'], [['data' => [$dinas, $cuti, $izin, $training, $dispensasi, $detasering], 'backgroundColor' => ['#1E3A8A', '#3B82F6', '#F97316', '#FDE047', '#22c55e', '#ef4444']]], 300, 300) : null;

        // Anggaran Charts (per kategori)
        foreach($anggaranData['perKategori'] as $kat => $items) {
            $real = $items->sum('realisasi') + $items->sum('komitmen');
            $sisa = $items->sum('rkap') - $real;
            $safeKat = str_replace([' ', '&'], '', $kat);
            $dataLaporan["chartAnggaran$safeKat"] = ($real > 0 || $sisa > 0) ? $generateChart('pie', ['Sisa', 'Penggunaan'], [['data' => [$sisa, $real], 'backgroundColor' => ['#1E3A8A', '#F97316']]], 300, 300) : null;
        }
        // Ensure all chart keys exist even if empty
        foreach (['Dikelola', 'Rutin', 'Investasi'] as $kat) {
            $safeKat = str_replace([' ', '&'], '', $kat);
            if (!isset($dataLaporan["chartAnggaran$safeKat"])) $dataLaporan["chartAnggaran$safeKat"] = null;
        }
        $dataLaporan['chartAnggaranDikelola'] = $dataLaporan['chartAnggaranDikelola'] ?? null;
        $dataLaporan['chartAnggaranRutin'] = $dataLaporan['chartAnggaranRutin'] ?? null;
        $dataLaporan['chartAnggaranInvestasi'] = $dataLaporan['chartAnggaranInvestasi'] ?? null;

        // BAR SK Memo Charts
        if ($barSkMemo) {
            $dataLaporan['imgBarSkMemoTerbit'] = $generateChart('bar', [$bulan], [
                ['label' => 'BAR Monitoring', 'data' => [$barSkMemo->bar_monitoring_terbit ?? 0], 'backgroundColor' => '#BAE6FD'],
                ['label' => 'Memo Direksi', 'data' => [$barSkMemo->memo_direksi_terbit ?? 0], 'backgroundColor' => '#3B82F6'],
                ['label' => 'SKD Kep. Bersama', 'data' => [$barSkMemo->skd_keputusan_bersama_terbit ?? 0], 'backgroundColor' => '#22C55E'],
                ['label' => 'SKD Non Ratifikasi', 'data' => [$barSkMemo->skd_non_ratifikasi_terbit ?? 0], 'backgroundColor' => '#F97316'],
                ['label' => 'SKD Ratifikasi', 'data' => [$barSkMemo->skd_ratifikasi_terbit ?? 0], 'backgroundColor' => '#1E3A8A'],
                ['label' => 'BAR Manajemen', 'data' => [$barSkMemo->bar_manajemen_terbit ?? 0], 'backgroundColor' => '#FDE047']
            ]);
            $dataLaporan['imgBarSkMemoProses'] = $generateChart('bar', [$bulan], [
                ['label' => 'Proses BAR Monitor', 'data' => [$barSkMemo->proses_bar_monitoring ?? 0], 'backgroundColor' => '#BAE6FD'],
                ['label' => 'Proses Memo Direksi', 'data' => [$barSkMemo->proses_memo_direksi ?? 0], 'backgroundColor' => '#3B82F6'],
                ['label' => 'Proses SKD Kep. Bersama', 'data' => [$barSkMemo->proses_skd_keputusan_bersama ?? 0], 'backgroundColor' => '#22C55E'],
                ['label' => 'Proses SKD Non Ratifikasi', 'data' => [$barSkMemo->proses_skd_non_ratifikasi ?? 0], 'backgroundColor' => '#F97316'],
                ['label' => 'Proses SKD Ratifikasi', 'data' => [$barSkMemo->proses_skd_ratifikasi ?? 0], 'backgroundColor' => '#1E3A8A'],
                ['label' => 'Proses BAR Manajemen', 'data' => [$barSkMemo->proses_bar_manajemen ?? 0], 'backgroundColor' => '#FDE047']
            ]);
        } else { $dataLaporan['imgBarSkMemoTerbit'] = null; $dataLaporan['imgBarSkMemoProses'] = null; }

        // Surat Chart
        $surat = $dataLaporan['surat'];
        $dataLaporan['chartSurat'] = $surat->count() > 0 ? $generateChart('bar', [$bulan], [
            ['label' => 'Surat Masuk', 'data' => [$surat->where('jenis_surat', 'Surat Masuk')->count()], 'backgroundColor' => '#1E3A8A'],
            ['label' => 'Surat Keluar', 'data' => [$surat->where('jenis_surat', 'Surat Keluar')->count()], 'backgroundColor' => '#F97316']
        ]) : null;

        // Undangan Chart
        $dataLaporan['chartUndangan'] = ($undanganData['totalIntern'] > 0 || $undanganData['totalEkstern'] > 0) ? $generateChart('bar', [$bulan], [
            ['label' => 'Undangan Intern', 'data' => [$undanganData['totalIntern']], 'backgroundColor' => '#1E3A8A'],
            ['label' => 'Undangan Ekstern', 'data' => [$undanganData['totalEkstern']], 'backgroundColor' => '#F97316']
        ]) : null;

        // Fotocopy Chart
        $dataLaporan['chartFotocopy'] = $jasaFotocopySemua->count() > 0 ? $generateChart('bar', [$bulan], [['label' => 'Jumlah Pemakaian Jasa Fotocopy', 'data' => [$jasaFotocopySemua->sum('pemakaian_lembar')], 'backgroundColor' => '#F97316']]) : null;

        // Kearsipan Chart
        $paTekstualCollection = $dataLaporan['paTekstual']['tabel1']->concat($dataLaporan['paTekstual']['tabel2']);
        $dofCollection = $dataLaporan['dof']['tabel1']->concat($dataLaporan['dof']['tabel2']);
        $paTeknikCollection = $dataLaporan['paTeknik']['tabel1']->concat($dataLaporan['paTeknik']['tabel2']);
        $dataLaporan['chartKearsipan'] = ($paTekstualCollection->count() > 0 || $dofCollection->count() > 0) ? $generateChart('bar', [$bulan], [
            ['label' => 'Pusat Arsip', 'data' => [$paTekstualCollection->sum('jumlah')], 'backgroundColor' => '#3B82F6'],
            ['label' => 'Teknikal File', 'data' => [$paTeknikCollection->sum('jumlah')], 'backgroundColor' => '#F97316'],
            ['label' => 'DOF', 'data' => [$dofCollection->sum('jumlah')], 'backgroundColor' => '#FDE047']
        ]) : null;

        // Perizinan Chart - Statistik Perizinan Terbit (Semua Tahun), tidak terikat filter tahun/bulan laporan
        $statistikTerbitSemuaTahun = $dataLaporan['statistikTerbitSemuaTahun'];
        $dataLaporan['chartPerizinan'] = $statistikTerbitSemuaTahun->count() > 0 ? $generateChart('bar', $statistikTerbitSemuaTahun->pluck('tahun')->map(fn($t) => (string) $t)->toArray(), [
            ['label' => 'Produk', 'data' => $statistikTerbitSemuaTahun->pluck('produk')->toArray(), 'backgroundColor' => '#1E3A8A'],
            ['label' => 'Aset', 'data' => $statistikTerbitSemuaTahun->pluck('aset')->toArray(), 'backgroundColor' => '#3B82F6'],
            ['label' => 'Proyek', 'data' => $statistikTerbitSemuaTahun->pluck('proyek')->toArray(), 'backgroundColor' => '#F97316'],
            ['label' => 'Peralatan Pabrik', 'data' => $statistikTerbitSemuaTahun->pluck('peralatan_pabrik')->toArray(), 'backgroundColor' => '#FDE047'],
            ['label' => 'Adm & Lainnya', 'data' => $statistikTerbitSemuaTahun->pluck('adm')->toArray(), 'backgroundColor' => '#22C55E'],
        ], 600, 280) : null;

        // Pelaporan Chart
        $dataLaporan['chartPelaporan'] = $pelaporan->count() > 0 ? $generateChart('bar', [$bulan], [
            ['label' => 'Laporan Internal', 'data' => [$pelaporan->where('tujuan', 'Internal')->count()], 'backgroundColor' => '#1E3A8A'],
            ['label' => 'Laporan Eksternal', 'data' => [$pelaporan->where('tujuan', 'Eksternal')->count()], 'backgroundColor' => '#F97316']
        ]) : null;

        // Kurir Chart
        $jasaKurirChartData = [];
        foreach ($jasaKurirMaster as $master) {
            $sum = $jasaKurirRaw->where('jasa_kurir_id', $master->id)->sum('jumlah');
            if ($sum > 0) {
                $jasaKurirChartData[] = ['label' => $master->nama_kurir, 'data' => [$sum], 'backgroundColor' => '#F97316'];
            }
        }
        $dataLaporan['chartKurir'] = count($jasaKurirChartData) > 0 ? $generateChart('bar', [$bulan], $jasaKurirChartData) : null;

        // Pemeliharaan Chart
        $pemRutin = $dataLaporan['pemeliharaanRutin'];
        $pemPeralatan = $dataLaporan['pemeliharaanPeralatan'];
        $dataLaporan['chartPemeliharaan'] = ($pemRutin->count() > 0 || $pemPeralatan->count() > 0) ? $generateChart('bar', [$bulan], [
            ['label' => 'Pemeliharaan Furnitur', 'data' => [$pemRutin->sum('jumlah')], 'backgroundColor' => '#F97316'],
            ['label' => 'Penyiapan Peralatan', 'data' => [$pemPeralatan->sum('jumlah')], 'backgroundColor' => '#3B82F6']
        ]) : null;

        // Pengiriman Chart
        if ($pengirimanDokumen) {
            $dataLaporan['chartPengiriman'] = $generateChart('bar', [$bulan], [
                ['label' => 'Penerimaan Mailroom', 'data' => [$pengirimanDokumen->penerimaan_mailroom ?? 0], 'backgroundColor' => '#0056A3'],
                ['label' => 'Pengiriman Dalam Negeri', 'data' => [$pengirimanDokumen->pengiriman_dalam_negeri ?? 0], 'backgroundColor' => '#22C55E'],
                ['label' => 'Pengiriman Luar Negeri', 'data' => [$pengirimanDokumen->pengiriman_luar_negeri ?? 0], 'backgroundColor' => '#F87171'],
                ['label' => 'Reg. Surat Masuk DOF', 'data' => [$pengirimanDokumen->registrasi_surat_masuk_dof ?? 0], 'backgroundColor' => '#F7941E'],
            ]);
        } else {
            $dataLaporan['chartPengiriman'] = null;
        }

        // ==========================================
        // 3. GENERATE REPORT DENGAN DOMPDF (LANDSCAPE)
        // ==========================================
        $dompdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                  ->loadView('pdf.laporan-kinerja-bulanan', $dataLaporan)
                  ->setPaper('A4', 'landscape');
        
        $tempDomPdf = tempnam(sys_get_temp_dir(), 'dompdf_');
        file_put_contents($tempDomPdf, $dompdf->output());

        // ==========================================
        // 4. GABUNGKAN DENGAN COVER MENGGUNAKAN MPDF (PORTRAIT)
        // ==========================================
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-P']);
        
        $mpdf->AddPage();
        $mpdf->Image(public_path('images/cover-laporan.png'), 0, 0, 210, 297, 'png', '', true, false);
        
        $boxX = 18;  $boxY_Bulan = 182;  $boxY_Tahun = 208;
        $boxW = 80;  $boxH = 25;

        $mpdf->SetFillColor(43, 73, 143); 
        $mpdf->Rect($boxX, $boxY_Bulan, $boxW, $boxH, 'F');
        
        $mpdf->SetFillColor(255, 255, 255); 
        $mpdf->Rect($boxX, $boxY_Tahun, $boxW, $boxH, 'F');
        
        $mpdf->SetXY($boxX, $boxY_Bulan + 4);
        $mpdf->SetFont('Arial', 'B', 28);
        $mpdf->SetTextColor(255, 255, 255);
        $mpdf->Cell($boxW, 15, strtoupper($bulan), 0, 0, 'C');
        
        $mpdf->SetXY($boxX, $boxY_Tahun + 2);
        $mpdf->SetFont('Arial', 'B', 46);
        $mpdf->SetTextColor(30, 58, 138);
        $mpdf->Cell($boxW, 15, $tahun, 0, 0, 'C');

        $pageCount = $mpdf->setSourceFile($tempDomPdf);
        for ($i = 1; $i <= $pageCount; $i++) {
            $mpdf->AddPage('L');
            $tplId = $mpdf->importPage($i);
            $mpdf->UseTemplate($tplId);
        }

        unlink($tempDomPdf);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Laporan_Kinerja_ADKOR_'.$bulan.'_'.$tahun.'.pdf"'
        ]);
    }
}