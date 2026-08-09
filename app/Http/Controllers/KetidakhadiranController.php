<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Ketidakhadiran;
use App\Models\KetidakhadiranHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KetidakhadiranController extends Controller
{
    public function index(Request $request)
    {
        $bulanList = Ketidakhadiran::bulanList();
        $kategoriList = Ketidakhadiran::kategoriList();

        $tahun = (int) $request->input('tahun', now()->year);
        $bulanNama = $request->input('bulan', $bulanList[now()->month - 1]);

        if (!in_array($bulanNama, $bulanList, true)) {
            $bulanNama = $bulanList[now()->month - 1];
        }

        $karyawan = Karyawan::query()
            ->leftJoin('ketidakhadiran', function ($join) use ($tahun, $bulanNama) {
                $join->on('ketidakhadiran.karyawan_id', '=', 'karyawan.id')
                    ->where('ketidakhadiran.tahun', $tahun)
                    ->where('ketidakhadiran.bulan', $bulanNama);
            })
            ->select([
                'karyawan.id as karyawan_id',
                'karyawan.nama',
                'karyawan.npk',
                'ketidakhadiran.id as ketidakhadiran_id',
                'ketidakhadiran.keterangan',
                'ketidakhadiran.dinas',
                'ketidakhadiran.cuti',
                'ketidakhadiran.izin',
                'ketidakhadiran.training',
                'ketidakhadiran.dispensasi',
                'ketidakhadiran.detasering',
            ])
            ->orderBy('karyawan.nama')
            ->paginate(10)
            ->withQueryString();

        $totalPerKategori = [];
        foreach ($kategoriList as $kategori) {
            // FIX: Tambahkan whereHas('karyawan') agar data karyawan yang sudah terhapus tidak ikut terjumlah di Chart[cite: 31]
            $totalPerKategori[ucfirst($kategori)] = (int) Ketidakhadiran::whereHas('karyawan')
                ->where('tahun', $tahun)
                ->where('bulan', $bulanNama)
                ->sum($kategori);
        }

        $totalHari = array_sum($totalPerKategori);

        $daftarKaryawan = Karyawan::orderBy('nama')->get(['id', 'nama', 'npk']);

        return view('ketidakhadiran.index', [
            'karyawan' => $karyawan,
            'totalPerKategori' => $totalPerKategori,
            'totalHari' => $totalHari,
            'tahun' => $tahun,
            'bulanNama' => $bulanNama,
            'bulanList' => $bulanList,
            'daftarKaryawan' => $daftarKaryawan,
        ]);
    }

    public function storeBulanan(Request $request)
    {
        $kategoriList = Ketidakhadiran::kategoriList();

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bulan' => 'required|in:' . implode(',', Ketidakhadiran::bulanList()),
            'keterangan' => 'nullable|string|max:255',
            'dinas' => 'nullable|integer|min:0',
            'cuti' => 'nullable|integer|min:0',
            'izin' => 'nullable|integer|min:0',
            'training' => 'nullable|integer|min:0',
            'dispensasi' => 'nullable|integer|min:0',
            'detasering' => 'nullable|integer|min:0',
        ]);

        $angka = [];
        foreach ($kategoriList as $kategori) {
            $angka[$kategori] = $validated[$kategori] ?? 0;
        }

        Ketidakhadiran::updateOrCreate(
            [
                'karyawan_id' => $validated['karyawan_id'],
                'tahun' => $validated['tahun'],
                'bulan' => $validated['bulan'],
            ],
            array_merge($angka, [
                'keterangan' => $validated['keterangan'] ?? null,
            ])
        );

        return redirect()
            ->route('ketidakhadiran.index', ['tahun' => $validated['tahun'], 'bulan' => $validated['bulan']])
            ->with('success', 'Data ketidakhadiran bulanan berhasil disimpan.');
    }

    public function destroyBulanan(Ketidakhadiran $ketidakhadiran)
    {
        $tahun = $ketidakhadiran->tahun;
        $bulanNama = $ketidakhadiran->bulan;
        $ketidakhadiran->delete();

        return redirect()
            ->route('ketidakhadiran.index', ['tahun' => $tahun, 'bulan' => $bulanNama])
            ->with('success', 'Data ketidakhadiran bulanan berhasil dihapus.');
    }

    public function storeHarian(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'jenis' => 'required|in:' . implode(',', Ketidakhadiran::kategoriList()),
            'keterangan' => 'nullable|string|max:255',
        ]);

        $tanggal = Carbon::parse($validated['tanggal']);
        $tahun = $tanggal->year;
        $bulanNama = Ketidakhadiran::bulanList()[$tanggal->month - 1];

        $existing = KetidakhadiranHarian::where('karyawan_id', $validated['karyawan_id'])
            ->whereDate('tanggal', $tanggal->toDateString())
            ->first();

        if ($existing) {
            if ($existing->jenis !== $validated['jenis']) {
                $this->adjustBulanan($validated['karyawan_id'], $tahun, $bulanNama, $existing->jenis, -1);
                $this->adjustBulanan($validated['karyawan_id'], $tahun, $bulanNama, $validated['jenis'], 1);
            }
            $existing->update([
                'jenis' => $validated['jenis'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        } else {
            KetidakhadiranHarian::create([
                'karyawan_id' => $validated['karyawan_id'],
                'tanggal' => $tanggal->toDateString(),
                'jenis' => $validated['jenis'],
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
            $this->adjustBulanan($validated['karyawan_id'], $tahun, $bulanNama, $validated['jenis'], 1);
        }

        return redirect()
            ->route('ketidakhadiran.index', ['tahun' => $tahun, 'bulan' => $bulanNama])
            ->with('success', 'Data ketidakhadiran harian berhasil disimpan, rekap bulanan otomatis bertambah.');
    }

    public function destroyHarian(KetidakhadiranHarian $harian)
    {
        $tahun = $harian->tanggal->year;
        $bulanNama = Ketidakhadiran::bulanList()[$harian->tanggal->month - 1];
        $karyawanId = $harian->karyawan_id;

        $this->adjustBulanan($karyawanId, $tahun, $bulanNama, $harian->jenis, -1);
        $harian->delete();

        return redirect()
            ->route('ketidakhadiran.harian', ['karyawan_id' => $karyawanId, 'tahun' => $tahun, 'bulan' => $bulanNama])
            ->with('success', 'Data harian dihapus, rekap bulanan otomatis dikurangi.');
    }

    public function harian(Request $request)
    {
        $bulanList = Ketidakhadiran::bulanList();

        $karyawanId = $request->input('karyawan_id');
        $tahun = (int) $request->input('tahun', now()->year);
        $bulanNama = $request->input('bulan', $bulanList[now()->month - 1]);

        if (!in_array($bulanNama, $bulanList, true)) {
            $bulanNama = $bulanList[now()->month - 1];
        }

        $bulanNumber = array_search($bulanNama, $bulanList) + 1;

        $karyawanTerpilih = $karyawanId ? Karyawan::find($karyawanId) : null;

        $riwayat = collect();
        if ($karyawanTerpilih) {
            $riwayat = KetidakhadiranHarian::where('karyawan_id', $karyawanId)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulanNumber)
                ->orderBy('tanggal')
                ->get();
        }

        $daftarKaryawan = Karyawan::orderBy('nama')->get(['id', 'nama', 'npk']);

        return view('ketidakhadiran.harian', [
            'karyawanTerpilih' => $karyawanTerpilih,
            'riwayat' => $riwayat,
            'tahun' => $tahun,
            'bulanNama' => $bulanNama,
            'bulanList' => $bulanList,
            'daftarKaryawan' => $daftarKaryawan,
        ]);
    }

    protected function adjustBulanan(int $karyawanId, int $tahun, string $bulanNama, string $kategori, int $delta): void
    {
        $row = Ketidakhadiran::firstOrCreate(
            ['karyawan_id' => $karyawanId, 'tahun' => $tahun, 'bulan' => $bulanNama],
            array_fill_keys(Ketidakhadiran::kategoriList(), 0)
        );

        $row->{$kategori} = max(0, ((int) $row->{$kategori}) + $delta);
        $row->save();
    }
}