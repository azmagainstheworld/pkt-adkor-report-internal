<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JasaKurirMaster;
use App\Models\JasaKurirData;
use Illuminate\Support\Facades\DB;

class JasaKurirController extends Controller
{
    /**
     * Menampilkan halaman Jasa Kurir beserta grafik dan tabel
     */
    public function index(Request $request)
    {
        // 1. Ambil Filter Tahun dan Bulan
        $tahunFilter = $request->input('tahun', date('Y'));
        $bulanFilter = $request->input('bulan', 'semua');

        // 2. Ambil Master Kurir yang Aktif untuk header tabel dan legenda
        $kurirMaster = JasaKurirMaster::where('aktif', true)->orderBy('id')->get();

        // 3. Ambil Data Pengiriman berdasarkan filter
        $query = JasaKurirData::with('jasaKurirMaster')->where('tahun', $tahunFilter);
        
        if ($bulanFilter != 'semua') {
            $query->where('bulan', $bulanFilter);
        }
        
        $rawData = $query->get();

        // 4. Format Data untuk Tabel (Pivot Data)
        // Mengelompokkan data berdasarkan Tahun dan Bulan
        $tableData = [];
        foreach ($rawData as $data) {
            $key = $data->tahun . '-' . $data->bulan;
            
            if (!isset($tableData[$key])) {
                $tableData[$key] = [
                    'tahun' => $data->tahun,
                    'bulan' => $data->bulan,
                    'total_semua' => 0
                ];
                // Inisialisasi semua kurir dengan 0
                foreach ($kurirMaster as $kurir) {
                    $tableData[$key]['kurir_' . $kurir->id] = 0;
                }
            }

            // Isi nilai jumlah pengiriman
            $tableData[$key]['kurir_' . $data->jasa_kurir_id] = $data->jumlah;
            $tableData[$key]['total_semua'] += $data->jumlah;
        }

        // 5. Format Data untuk Chart (Grouped Bar Chart)
        $chartData = [];
        $daftarBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        foreach ($daftarBulan as $bulan) {
            $bulanData = ['bulan' => $bulan];
            foreach ($kurirMaster as $kurir) {
                // Cari data pengiriman untuk kurir dan bulan ini
                $jumlah = $rawData->where('bulan', $bulan)
                                  ->where('jasa_kurir_id', $kurir->id)
                                  ->first()->jumlah ?? 0;
                $bulanData[$kurir->nama_kurir] = $jumlah;
            }
            $chartData[] = $bulanData;
        }

        // Return ke view (pastikan nama view sesuai, misalnya 'jasakurir')
        return view('jasakurir', compact('kurirMaster', 'tableData', 'chartData', 'tahunFilter', 'bulanFilter'));
    }

    /**
     * Menyimpan data Jasa Kurir (Master) baru
     */
    public function storeMaster(Request $request)
    {
        $request->validate([
            'nama_kurir' => 'required|string|max:50|unique:jasa_kurir_master,nama_kurir'
        ]);

        JasaKurirMaster::create([
            'nama_kurir' => $request->nama_kurir,
            'aktif' => true
        ]);

        return redirect()->back()->with('success', 'Jasa kurir baru berhasil ditambahkan.');
    }

    /**
     * Menyimpan atau mengupdate jumlah pengiriman per bulan
     */
    public function storeData(Request $request)
    {
        // Validasi disesuaikan karena input sekarang berupa satu Dropdown dan satu Jumlah
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string|in:Januari,Februari,Maret,April,Mei,Juni,Juli,Agustus,September,Oktober,November,Desember',
            'jasa_kurir_id' => 'required|exists:jasa_kurir_master,id',
            'jumlah' => 'required|integer|min:0'
        ]);

        // UpdateOrCreate mencegah duplikasi data untuk kurir, tahun, dan bulan yang sama
        JasaKurirData::updateOrCreate(
            [
                'jasa_kurir_id' => $request->jasa_kurir_id,
                'tahun' => $request->tahun,
                'bulan' => $request->bulan
            ],
            [
                'jumlah' => $request->jumlah
            ]
        );

        return redirect()->back()->with('success', 'Data jumlah pengiriman berhasil disimpan.');
    }
    
}