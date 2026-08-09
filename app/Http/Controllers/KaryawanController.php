<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\KeluargaKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('npk', 'like', "%{$search}%");
            });
        }

        $totalKaryawan = Karyawan::count();
        $totalKeluarga = KeluargaKaryawan::count(); 
        $totalJiwa     = $totalKaryawan + $totalKeluarga;

        $chartJenisData = Karyawan::select('keterangan', DB::raw('count(*) as total'))
            ->groupBy('keterangan')
            ->pluck('total', 'keterangan')
            ->all();

        $countOrganik = $chartJenisData['Organik'] ?? 0;
        $countNonOrganik = $chartJenisData['Non Organik'] ?? 0;

        $now = Carbon::now()->toDateString();
        $limaTahunLagi = Carbon::now()->addYears(5)->toDateString();
        $sepuluhTahunLagi = Carbon::now()->addYears(10)->toDateString();

        $rawSelectPensiun = "
            COUNT(CASE WHEN mpp_pbp <= :now THEN 1 END) as sudah_pensiun,
            COUNT(CASE WHEN mpp_pbp > :now2 AND mpp_pbp <= :limaTahun THEN 1 END) as kurang_5_tahun,
            COUNT(CASE WHEN mpp_pbp > :limaTahun2 AND mpp_pbp <= :sepuluhTahun THEN 1 END) as kurang_10_tahun,
            COUNT(CASE WHEN mpp_pbp > :sepuluhTahun2 THEN 1 END) as lebih_10_tahun
        ";

        $pensiunCountsDB = DB::table('karyawan')
            ->selectRaw($rawSelectPensiun)
            ->setBindings([
                'now' => $now,
                'now2' => $now, 
                'limaTahun' => $limaTahunLagi,
                'limaTahun2' => $limaTahunLagi,
                'sepuluhTahun' => $sepuluhTahunLagi,
                'sepuluhTahun2' => $sepuluhTahunLagi,
            ])
            ->whereNull('deleted_at')
            ->first();

        $chartPensiunData = [
            'Sudah Pensiun' => $pensiunCountsDB->sudah_pensiun,
            '< 5 Tahun'     => $pensiunCountsDB->kurang_5_tahun,
            '< 10 Tahun'    => $pensiunCountsDB->kurang_10_tahun,
            '> 10 Tahun'    => $pensiunCountsDB->lebih_10_tahun,
        ];

        $pensiunShort = $chartPensiunData['< 5 Tahun'];
        $pensiunMedium = $chartPensiunData['< 10 Tahun'];
        $pensiunLong = $chartPensiunData['> 10 Tahun'];

        $kaosCountsDB = Karyawan::select('ukuran_kaos', DB::raw('count(*) as total'))
            ->whereNotNull('ukuran_kaos')
            ->groupBy('ukuran_kaos')
            ->pluck('total', 'ukuran_kaos')
            ->all();

        $masterUkuran = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

        $statisRekapKaos = [];
        foreach ($masterUkuran as $ukuran) {
            $statisRekapKaos[] = (object) [
                'ukuran_kaos' => $ukuran,
                'total'       => $kaosCountsDB[$ukuran] ?? 0,
            ];
        }

        $rekapKaos = collect($statisRekapKaos);

        $karyawan = $query->orderBy('nama', 'asc')->paginate(10)->appends($request->all());

        return view('karyawan', compact(
            'karyawan', 'totalKaryawan', 'totalJiwa', 'chartJenisData',
            'chartPensiunData', 'rekapKaos', 'countOrganik', 'countNonOrganik',
            'pensiunLong', 'pensiunShort', 'pensiunMedium'
        ));
    }

    public function create() { return view('karyawan.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:150',
            'npk'              => ['required','string','max:30', Rule::unique('karyawan', 'npk')->whereNull('deleted_at')],
            'tempat_lahir'     => 'nullable|string|max:100',
            'tanggal_lahir'    => 'nullable|date',
            'no_ptk'           => 'nullable|string|max:50',
            'no_hp'            => 'nullable|string|max:20',
            'foto'             => 'nullable|string', 
            'ket_pensiun'      => 'required|string|in:> 10 Tahun,< 10 Tahun,< 5 Tahun',            
            'gol_grade'        => 'required|string|max:10',
            'mpp_pbp'          => 'required|date', 
            'keterangan'       => 'required|string|in:Organik,Non Organik',
            'alamat'           => 'required|string',
            'ukuran_kaos'      => 'required|string|max:10',
            'status'           => 'nullable|string|max:50',
        ]);

        Karyawan::create($validated);
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan!');
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load('keluarga');
        return view('show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan) { return view('edit', compact('karyawan')); }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:150',
            'npk'              => ['required','string','max:30', Rule::unique('karyawan', 'npk')->ignore($karyawan->id)->whereNull('deleted_at')],
            'tempat_lahir'     => 'nullable|string|max:100',
            'tanggal_lahir'    => 'nullable|date',
            'no_ptk'           => 'nullable|string|max:50',
            'no_hp'            => 'nullable|string|max:20',
            'foto'             => 'nullable|string',
            'ket_pensiun'      => 'required|string|in:> 10 Tahun,< 10 Tahun,< 5 Tahun',            
            'gol_grade'        => 'required|string|max:10',
            'mpp_pbp'          => 'required|date',
            'keterangan'       => 'required|string|in:Organik,Non Organik',
            'alamat'           => 'required|string',
            'ukuran_kaos'      => 'required|string|max:10',
            'status'           => 'nullable|string|max:50',
        ]);

        $karyawan->update($validated);
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus!');
    }

    // ========================================================================
    // METHOD KELUARGA KARYAWAN DENGAN TRY-CATCH (ANTI LAYAR ERROR 500)
    // ========================================================================

    public function storeKeluarga(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:150',
            'hubungan'      => 'required|in:Suami,Istri,Anak',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('karyawan.show', $id)->withErrors($validator, 'keluarga')->withInput();
        }

        try {
            $karyawan = Karyawan::findOrFail($id);
            $karyawan->keluarga()->create($validator->validated());
        } catch (\Exception $e) {
            // Jika database menolak (misal kolom tempat_lahir belum dibikin di phpmyadmin)
            // Sistem tidak akan crash, melainkan menampilkan error rapi ke user.
            return redirect()->route('karyawan.show', $id)
                             ->with('error_db', 'Gagal menyimpan ke database! Error: ' . $e->getMessage());
        }

        return redirect()->route('karyawan.show', $id)->with('success', 'Data anggota keluarga berhasil ditambahkan!');
    }

    public function updateKeluarga(Request $request, $id)
    {
        $keluarga = KeluargaKaryawan::findOrFail($id);
        $karyawanId = $keluarga->karyawan_id;

        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:150',
            'hubungan'      => 'required|in:Suami,Istri,Anak',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('karyawan.show', $karyawanId)->withErrors($validator, 'keluarga')->withInput();
        }

        try {
            $keluarga->update($validator->validated());
        } catch (\Exception $e) {
            return redirect()->route('karyawan.show', $karyawanId)
                             ->with('error_db', 'Gagal update database! Error: ' . $e->getMessage());
        }

        return redirect()->route('karyawan.show', $karyawanId)->with('success', 'Data anggota keluarga berhasil diperbarui!');
    }

    public function destroyKeluarga($id)
    {
        $keluarga = KeluargaKaryawan::findOrFail($id);
        $karyawanId = $keluarga->karyawan_id;
        $keluarga->delete();

        return redirect()->route('karyawan.show', $karyawanId)->with('success', 'Data anggota keluarga berhasil dihapus!');
    }
}