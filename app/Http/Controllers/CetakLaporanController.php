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
        // 1. TARIK DATA DARI DATABASE
        // ==========================================
        $dataLaporan['programStrategis'] = Schema::hasTable('program_strategis') ? DB::table('program_strategis')->where('tahun', $tahun)->where('bulan', $bulan)->orWhereNull('bulan')->get() : collect([]);
        $karyawan = Schema::hasTable('karyawan') ? DB::table('karyawan')->where('status', 'aktif')->whereNull('deleted_at')->get() : collect([]);
        $dataLaporan['karyawan'] = $karyawan;
        
        $ketidakhadiran = Schema::hasTable('ketidakhadiran') ? DB::table('ketidakhadiran')->join('karyawan', 'ketidakhadiran.karyawan_id', '=', 'karyawan.id')->where('ketidakhadiran.tahun', $tahun)->where('ketidakhadiran.bulan', $bulan)->select('ketidakhadiran.*', 'karyawan.nama', 'karyawan.npk')->get() : collect([]);
        $dataLaporan['ketidakhadiran'] = $ketidakhadiran;

        $anggaran = Schema::hasTable('anggaran_administrasi') ? DB::table('anggaran_administrasi')->where('tahun', $tahun)->where('bulan', $bulan)->get() : collect([]);
        $dataLaporan['anggaran'] = $anggaran;

        $perizinan = Schema::hasTable('perizinan_terbit') ? DB::table('perizinan_terbit')->whereYear('tanggal_sejak', $tahun)->get() : collect([]);
        $dataLaporan['perizinan'] = $perizinan;

        $pelaporan = Schema::hasTable('pelaporan') ? DB::table('pelaporan')->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulanNum)->get() : collect([]);
        $dataLaporan['pelaporan'] = $pelaporan;

        $dataLaporan['paTekstual'] = Schema::hasTable('pa_tekstual_data') ? DB::table('pa_tekstual_data')->join('pa_tekstual_master', 'pa_tekstual_data.master_id', '=', 'pa_tekstual_master.id')->where('pa_tekstual_data.tahun', $tahun)->where('pa_tekstual_data.bulan', $bulan)->get() : collect([]);
        $dataLaporan['paNonTekstual'] = Schema::hasTable('pa_non_tekstual_values') ? DB::table('pa_non_tekstual_values')->join('pa_non_tekstual_types', 'pa_non_tekstual_values.type_id', '=', 'pa_non_tekstual_types.id')->where('pa_non_tekstual_values.tahun', $tahun)->where('pa_non_tekstual_values.bulan', $bulan)->select('pa_non_tekstual_values.*', 'pa_non_tekstual_types.name as jenis')->get() : collect([]);
        $dataLaporan['paTeknik'] = Schema::hasTable('pa_teknik_data') ? DB::table('pa_teknik_data')->join('pa_teknik_masters', 'pa_teknik_data.master_id', '=', 'pa_teknik_masters.id')->where('pa_teknik_data.tahun', $tahun)->where('pa_teknik_data.bulan', $bulan)->get() : collect([]);
        $dataLaporan['dof'] = Schema::hasTable('dof_data') ? DB::table('dof_data')->join('dof_masters', 'dof_data.master_id', '=', 'dof_masters.id')->where('dof_data.tahun', $tahun)->where('dof_data.bulan', $bulan)->get() : collect([]);

        $surat = Schema::hasTable('surat') ? DB::table('surat')->where('tahun', $tahun)->where('bulan', $bulan)->get() : collect([]);
        $dataLaporan['surat'] = $surat;

        $jasaKurir = Schema::hasTable('jasa_kurir_data') ? DB::table('jasa_kurir_data')->join('jasa_kurir_master', 'jasa_kurir_data.jasa_kurir_id', '=', 'jasa_kurir_master.id')->where('jasa_kurir_data.tahun', $tahun)->where('jasa_kurir_data.bulan', $bulan)->get() : collect([]);
        $dataLaporan['jasaKurir'] = $jasaKurir;

        $fotocopy = Schema::hasTable('jasa_fotocopy') ? DB::table('jasa_fotocopy')->where('tahun', $tahun)->where('bulan', $bulan)->get() : collect([]);
        $dataLaporan['fotocopy'] = $fotocopy;

        $pemeliharaanRutin = (Schema::hasTable('pemeliharaan_rutin_data') && Schema::hasTable('pemeliharaan_rutin_master')) ? DB::table('pemeliharaan_rutin_data')->join('pemeliharaan_rutin_master', 'pemeliharaan_rutin_data.rutin_id', '=', 'pemeliharaan_rutin_master.id')->where('pemeliharaan_rutin_data.tahun', $tahun)->where('pemeliharaan_rutin_data.bulan', $bulan)->select('pemeliharaan_rutin_data.*', 'pemeliharaan_rutin_master.nama_pemeliharaan')->get() : collect([]);
        $dataLaporan['pemeliharaanRutin'] = $pemeliharaanRutin;
                
        $pemeliharaanPeralatan = (Schema::hasTable('pemeliharaan_peralatan_data') && Schema::hasTable('pemeliharaan_peralatan_master')) ? DB::table('pemeliharaan_peralatan_data')->join('pemeliharaan_peralatan_master', 'pemeliharaan_peralatan_data.peralatan_id', '=', 'pemeliharaan_peralatan_master.id')->where('pemeliharaan_peralatan_data.tahun', $tahun)->where('pemeliharaan_peralatan_data.bulan', $bulan)->select('pemeliharaan_peralatan_data.*', 'pemeliharaan_peralatan_master.nama_peralatan')->get() : collect([]);
        $dataLaporan['pemeliharaanPeralatan'] = $pemeliharaanPeralatan;

        $undangan = Schema::hasTable('undangan') ? DB::table('undangan')->where('tahun', $tahun)->where('bulan', $bulan)->get() : collect([]);
        $dataLaporan['undangan'] = $undangan;

        $barSkMemo = Schema::hasTable('bar_sk_memo') ? DB::table('bar_sk_memo')->where('tahun', $tahun)->where('bulan', $bulan)->first() : null;
        $dataLaporan['barSkMemo'] = $barSkMemo;

        $dataLaporan['kendala'] = Schema::hasTable('masalah_kendala') ? DB::table('masalah_kendala')->where('tahun', $tahun)->where('bulan', $bulan)->get() : collect([]);


        // ==========================================
        // 2. GENERATE GRAFIK BASE64 (DIJAMIN MUNCUL DI PDF)
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

        // SDM (Pie)
        $org = $karyawan->where('keterangan', 'Organik')->count();
        $nonOrg = $karyawan->where('keterangan', 'Non Organik')->count();
        $dataLaporan['chartSdm'] = ($org > 0 || $nonOrg > 0) ? $generateChart('pie', ['Organik', 'Non Organik'], [['data' => [$org, $nonOrg], 'backgroundColor' => ['#F97316', '#1E3A8A']]], 300, 300) : null;

        $dinas = $ketidakhadiran->sum('dinas'); $cuti = $ketidakhadiran->sum('cuti'); $izin = $ketidakhadiran->sum('izin'); $training = $ketidakhadiran->sum('training');
        $dataLaporan['chartAbsensi'] = ($dinas > 0 || $cuti > 0 || $izin > 0 || $training > 0) ? $generateChart('pie', ['Dinas', 'Cuti', 'Izin', 'Training'], [['data' => [$dinas, $cuti, $izin, $training], 'backgroundColor' => ['#1E3A8A', '#3B82F6', '#F97316', '#FDE047']]], 300, 300) : null;

        // Anggaran (Pie)
        foreach(['Dikelola', 'Rutin', 'Investasi'] as $kat) {
            $real = $anggaran->where('kategori', "Anggaran $kat")->sum('realisasi') + $anggaran->where('kategori', "Anggaran $kat")->sum('komitmen');
            $sisa = $anggaran->where('kategori', "Anggaran $kat")->sum('rkap') - $real;
            $dataLaporan["chartAnggaran$kat"] = ($real > 0 || $sisa > 0) ? $generateChart('pie', ['Sisa', 'Penggunaan'], [['data' => [$sisa, $real], 'backgroundColor' => ['#1E3A8A', '#F97316']]], 300, 300) : null;
        }

        // BAR SK Memo (Bar)
        if ($barSkMemo) {
            $dataLaporan['imgBarSkMemoTerbit'] = $generateChart('bar', [$bulan], [
                ['label' => 'BAR Monitoring', 'data' => [$barSkMemo->bar_monitoring_terbit], 'backgroundColor' => '#BAE6FD'],
                ['label' => 'Memo Direksi', 'data' => [$barSkMemo->memo_direksi_terbit], 'backgroundColor' => '#3B82F6'],
                ['label' => 'SKD Kep. Bersama', 'data' => [$barSkMemo->skd_keputusan_bersama_terbit], 'backgroundColor' => '#22C55E'],
                ['label' => 'SKD Non Ratifikasi', 'data' => [$barSkMemo->skd_non_ratifikasi_terbit], 'backgroundColor' => '#F97316'],
                ['label' => 'SKD Ratifikasi', 'data' => [$barSkMemo->skd_ratifikasi_terbit], 'backgroundColor' => '#1E3A8A'],
                ['label' => 'BAR Manajemen', 'data' => [$barSkMemo->bar_manajemen_terbit], 'backgroundColor' => '#FDE047']
            ]);
            $dataLaporan['imgBarSkMemoProses'] = $generateChart('bar', [$bulan], [
                ['label' => 'Proses BAR Monitor', 'data' => [$barSkMemo->proses_bar_monitoring], 'backgroundColor' => '#BAE6FD'],
                ['label' => 'Proses Memo Direksi', 'data' => [$barSkMemo->proses_memo_direksi], 'backgroundColor' => '#3B82F6'],
                ['label' => 'Proses SKD Kep. Bersama', 'data' => [$barSkMemo->proses_skd_keputusan_bersama], 'backgroundColor' => '#22C55E'],
                ['label' => 'Proses SKD Non Ratifikasi', 'data' => [$barSkMemo->proses_skd_non_ratifikasi], 'backgroundColor' => '#F97316'],
                ['label' => 'Proses SKD Ratifikasi', 'data' => [$barSkMemo->proses_skd_ratifikasi], 'backgroundColor' => '#1E3A8A'],
                ['label' => 'Proses BAR Manajemen', 'data' => [$barSkMemo->proses_bar_manajemen], 'backgroundColor' => '#FDE047']
            ]);
        } else { $dataLaporan['imgBarSkMemoTerbit'] = null; $dataLaporan['imgBarSkMemoProses'] = null; }

        // Modul Lainnya (Bar Chart Biasa)
        $dataLaporan['chartSurat'] = $surat->count() > 0 ? $generateChart('bar', [$bulan], [
            ['label' => 'Surat Masuk', 'data' => [$surat->where('jenis_surat', 'Surat Masuk')->count()], 'backgroundColor' => '#1E3A8A'],
            ['label' => 'Surat Keluar', 'data' => [$surat->where('jenis_surat', 'Surat Keluar')->count()], 'backgroundColor' => '#F97316']
        ]) : null;

        $dataLaporan['chartUndangan'] = $undangan->count() > 0 ? $generateChart('bar', [$bulan], [
            ['label' => 'Undangan Intern', 'data' => [$undangan->where('jenis', 'Internal')->count()], 'backgroundColor' => '#1E3A8A'],
            ['label' => 'Undangan Ekstern', 'data' => [$undangan->where('jenis', 'Eksternal')->count()], 'backgroundColor' => '#F97316']
        ]) : null;

        $dataLaporan['chartFotocopy'] = $fotocopy->count() > 0 ? $generateChart('bar', [$bulan], [['label' => 'Jumlah Pemakaian Jasa Fotocopy', 'data' => [$fotocopy->sum('pemakaian_lembar')], 'backgroundColor' => '#F97316']]) : null;
        
        $dataLaporan['chartKearsipan'] = ($dataLaporan['paTekstual']->count() > 0 || $dataLaporan['dof']->count() > 0) ? $generateChart('bar', [$bulan], [
            ['label' => 'Pusat Arsip', 'data' => [$dataLaporan['paTekstual']->sum('jumlah')], 'backgroundColor' => '#3B82F6'],
            ['label' => 'Teknikal File', 'data' => [$dataLaporan['paTeknik']->sum('jumlah')], 'backgroundColor' => '#F97316'],
            ['label' => 'DOF', 'data' => [$dataLaporan['dof']->sum('jumlah')], 'backgroundColor' => '#FDE047']
        ]) : null;

        $dataLaporan['chartPerizinan'] = $perizinan->count() > 0 ? $generateChart('bar', [$bulan], [['label' => 'Perizinan Terbit', 'data' => [$perizinan->count()], 'backgroundColor' => '#22C55E']]) : null;
        $dataLaporan['chartPelaporan'] = $pelaporan->count() > 0 ? $generateChart('bar', [$bulan], [
            ['label' => 'Laporan Internal', 'data' => [$pelaporan->where('tujuan', 'Internal')->count()], 'backgroundColor' => '#1E3A8A'],
            ['label' => 'Laporan Eksternal', 'data' => [$pelaporan->where('tujuan', 'Eksternal')->count()], 'backgroundColor' => '#F97316']
        ]) : null;
        $dataLaporan['chartKurir'] = $jasaKurir->count() > 0 ? $generateChart('bar', [$bulan], [['label' => 'Pengiriman Kurir', 'data' => [$jasaKurir->sum('jumlah')], 'backgroundColor' => '#F97316']]) : null;
        $dataLaporan['chartPemeliharaan'] = ($pemeliharaanRutin->count() > 0 || $pemeliharaanPeralatan->count() > 0) ? $generateChart('bar', [$bulan], [
            ['label' => 'Pemeliharaan Furnitur', 'data' => [$pemeliharaanRutin->sum('jumlah')], 'backgroundColor' => '#F97316'],
            ['label' => 'Penyiapan Peralatan', 'data' => [$pemeliharaanPeralatan->sum('jumlah')], 'backgroundColor' => '#3B82F6']
        ]) : null;

        // ==========================================
        // 3. GENERATE PDF
        // ==========================================
        $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                  ->loadView('pdf.laporan-kinerja-bulanan', $dataLaporan)
                  ->setPaper('A4', 'landscape');
        
        return $pdf->stream("Laporan_Kinerja_ADKOR_{$bulan}_{$tahun}.pdf");
    }
}
