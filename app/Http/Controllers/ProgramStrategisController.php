<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProgramStrategis;
use Carbon\Carbon;

class ProgramStrategisController extends Controller
{
    public function index(Request $request)
    {
        $tanggalToday = Carbon::now()->locale('en')->translatedFormat('l, d F Y');
        
        // Ambil filter tahun dari request, default ke tahun berjalan jika tidak ada
        $filterTahun = $request->input('tahun', Carbon::now()->year);

        // Ambil daftar tahun unik yang ada di database untuk mengisi dropdown
        $tahunTersedia = ProgramStrategis::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        if (empty($tahunTersedia) || !in_array(Carbon::now()->year, $tahunTersedia)) {
            $tahunTersedia[] = Carbon::now()->year; // Pastikan tahun ini selalu ada di opsi
            rsort($tahunTersedia);
        }

        // Jika filter "semua", ambil semua data. Jika tahun tertentu, filter berdasarkan tahun.
        $query = ProgramStrategis::query();
        if ($filterTahun !== 'semua') {
            $query->where('tahun', $filterTahun);
        }
        $dataProgram = $query->orderBy('id', 'asc')->get();

        return view('program-strategis', compact('tanggalToday', 'filterTahun', 'tahunTersedia', 'dataProgram'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'program_strategis' => 'required|string',
        ], [
            'tahun.required' => 'Tahun pelaksanaan wajib diisi.',
            'program_strategis.required' => 'Nama Program Strategis wajib diisi.',
        ]);

        ProgramStrategis::create($request->all());

        return back()->with('success', 'Program Strategis baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'program_strategis' => 'required|string',
        ]);

        $program = ProgramStrategis::findOrFail($id);
        $program->update($request->all());

        return back()->with('success', 'Data Program Strategis berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $program = ProgramStrategis::findOrFail($id);
        $program->delete();

        return back()->with('success', 'Program Strategis berhasil dihapus.');
    }
}