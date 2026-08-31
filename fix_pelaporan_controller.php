<?php
$content = file_get_contents('app/Http/Controllers/PelaporanController.php');

$functions = <<<CODE
    public function storeKolomDinamis(Request \$request)
    {
        \$request->validate([
            'modul'      => 'required|string',
            'nama_kolom' => 'required|string|max:100',
            'tipe_input' => 'required|in:text,number,date,dropdown,currency',
        ]);

        \$isDuplicate = \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('modul', \$request->modul)
            ->whereRaw('LOWER(nama_kolom) = ?', [strtolower(trim(\$request->nama_kolom))])->exists();

        if (\$isDuplicate) return back()->with('error_modal', 'Kolom sudah ada!');

        \$pilihanDropdown = null;
        if (\$request->tipe_input === 'dropdown' && \$request->pilihan_dropdown) {
            \$pilihanDropdown = json_encode(array_map('trim', explode(',', \$request->pilihan_dropdown)));
        }

        \Illuminate\Support\Facades\DB::table('dynamic_columns')->insert([
            'modul' => \$request->modul, 'nama_kolom' => trim(\$request->nama_kolom),
            'tipe_input' => \$request->tipe_input, 'pilihan_dropdown' => \$pilihanDropdown,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return back()->with('success', 'Kolom dinamis baru berhasil ditambahkan.');
    }

    public function destroyKolomDinamis(\$id)
    {
        \Illuminate\Support\Facades\DB::table('dynamic_columns')->where('id', \$id)->delete();
        return back()->with('success', 'Kolom dinamis berhasil dihapus.');
    }

    public function import(Request \$request)
    {
        set_time_limit(0);
        \$request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:51200']);
        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\PelaporanImport, \$request->file('file'));
            if (\$request->ajax() || \$request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back()->with('success', 'Data Pelaporan berhasil di-import!');
        } catch (\Exception \$e) {
            if (\$request->ajax() || \$request->wantsJson()) {
                return response()->json(['error' => 'Gagal mengimpor data: ' . \$e->getMessage()], 500);
            }
            return redirect()->back()->with('error_modal', 'Terjadi kesalahan saat import: ' . \$e->getMessage());
        }
    }

    public function exportExcel(Request \$request)
    {
        \$tahun = \$request->input('tahun', 'semua');
        \$bulan = \$request->input('bulan', 'semua');
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PelaporanExport(false, \$tahun, \$bulan), 'Data_Pelaporan.xlsx');
    }

    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PelaporanExport(true), 'Template_Import_Pelaporan.xlsx');
    }

    public function exportPdf(Request \$request)
    {
        \$tahun = \$request->input('tahun', 'semua');
        \$bulan = \$request->input('bulan', 'semua');

        \$query = \App\Models\Pelaporan::query();
        if (\$tahun !== 'semua') \$query->whereYear('tanggal', \$tahun);
        if (\$bulan !== 'semua') {
            \$mapBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
            if (isset(\$mapBulan[\$bulan])) \$query->whereMonth('tanggal', \$mapBulan[\$bulan]);
        }
        \$dataRincian = \$query->orderBy('tanggal', 'desc')->get();
        
        \$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pelaporan', compact('dataRincian'))->setPaper('a4', 'landscape');
        return \$pdf->download('Data_Pelaporan.pdf');
    }
}
CODE;

$content = preg_replace('/public function import\(Request \$request\)\s*\{\s*set_time_limit\(0\);\s*\$request->validate\(\[\'file\' => \'required\|mimes:xlsx,xls,csv\|max:51200\'\]\);\s*try \{\s*Excel::import\(new PelaporanImport, \$request->file\(\'file\'\)\);\s*if \(\$request->ajax\(\) \|\| \$request->wantsJson\(\)\) \{\s*return response\(\)->json\(\[\'success\' => true\]\);\s*\}\s*return redirect\(\)->back\(\)->with\(\'success\', \'Data Pelaporan berhasil di-import!\'\);\s*\} catch \(\\\\Exception \$e\) \{\s*if \(\$request->ajax\(\) \|\| \$request->wantsJson\(\)\) \{\s*return response\(\)->json\(\[\'error\' => \'Gagal mengimpor data: \' \. \$e->getMessage\(\)\], 500\);\s*\}\s*return redirect\(\)->back\(\)->with\(\'error_modal\', \'Terjadi kesalahan saat import: \' \. \$e->getMessage\(\)\);\s*\}\s*\}/', '', $content);

$content = preg_replace('/\}\s*$/s', "\n$functions", $content);

file_put_contents('app/Http/Controllers/PelaporanController.php', $content);
echo "PelaporanController restored.\n";
