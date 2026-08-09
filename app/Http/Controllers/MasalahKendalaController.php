<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasalahKendala;
use Carbon\Carbon;

class MasalahKendalaController extends Controller
{
    public function index(Request $request)
    {
        // Format Tanggal Inggris
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');

        $filterTahun = $request->input('tahun', 'semua');
        $filterBulan = $request->input('bulan', 'semua');

        $tahunTersedia = MasalahKendala::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia)) $tahunTersedia = [Carbon::now()->year];

        $query = MasalahKendala::query();
        if ($filterTahun != 'semua') $query->where('tahun', $filterTahun);
        if ($filterBulan != 'semua') $query->where('bulan', $filterBulan);

        // Sorting agar urut dari bulan terbaru
        $monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        $dataMasalah = $query->get()->sortByDesc(function($item) use ($monthsOrder) {
            return sprintf('%04d%02d', $item->tahun, $monthsOrder[$item->bulan] ?? 0);
        });

        return view('masalah-kendala', compact('tanggalToday', 'filterTahun', 'filterBulan', 'tahunTersedia', 'dataMasalah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'masalah' => 'required|string',
            'solusi' => 'required|string',
        ]);

        MasalahKendala::create($request->all());
        return back()->with('success', 'Data Masalah / Kendala berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'masalah' => 'required|string',
            'solusi' => 'required|string',
        ]);

        MasalahKendala::findOrFail($id)->update($request->all());
        return back()->with('success', 'Data Masalah / Kendala berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MasalahKendala::findOrFail($id)->delete();
        return back()->with('success', 'Data Masalah / Kendala berhasil dihapus.');
    }
}