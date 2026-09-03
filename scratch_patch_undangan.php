<?php

$controllerPath = 'app/Http/Controllers/UndanganController.php';
$content = file_get_contents($controllerPath);

// Define the missing methods
$missingMethods = <<<PHP
    private function recalculateRekap()
    {
        \$detailCounts = \App\Models\UndanganDetail::selectRaw('undangan_id, jenis_undangan, count(*) as total')
            ->groupBy('undangan_id', 'jenis_undangan')
            ->get();
            
        \App\Models\Undangan::query()->update(['undangan_intern' => 0, 'undangan_ekstern' => 0]);
        
        foreach (\$detailCounts as \$count) {
            \$undangan = \App\Models\Undangan::find(\$count->undangan_id);
            if (\$undangan) {
                if (strtolower(\$count->jenis_undangan) == 'intern') {
                    \$undangan->undangan_intern = \$count->total;
                } else {
                    \$undangan->undangan_ekstern = \$count->total;
                }
                \$undangan->save();
            }
        }
    }

    public function storeDetail(Request \$request)
    {
        \$request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'jenis_undangan' => 'required|string',
            'agenda' => 'required|string',
        ]);

        \$undangan = \App\Models\Undangan::firstOrCreate(
            ['tahun' => \$request->tahun, 'bulan' => \$request->bulan],
            ['undangan_intern' => 0, 'undangan_ekstern' => 0]
        );

        \App\Models\UndanganDetail::create([
            'undangan_id' => \$undangan->id,
            'jenis_undangan' => \$request->jenis_undangan,
            'agenda' => \$request->agenda,
        ]);

        \$this->recalculateRekap();

        return redirect()->back()->with('success', 'Berhasil menambahkan rincian undangan.');
    }

    public function updateDetail(Request \$request, \$id)
    {
        \$request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
            'jenis_undangan' => 'required|string',
            'agenda' => 'required|string',
        ]);

        \$detail = \App\Models\UndanganDetail::findOrFail(\$id);
        
        // Find or create the target Undangan
        \$undangan = \App\Models\Undangan::firstOrCreate(
            ['tahun' => \$request->tahun, 'bulan' => \$request->bulan],
            ['undangan_intern' => 0, 'undangan_ekstern' => 0]
        );

        \$detail->update([
            'undangan_id' => \$undangan->id,
            'jenis_undangan' => \$request->jenis_undangan,
            'agenda' => \$request->agenda,
        ]);

        \$this->recalculateRekap();

        return redirect()->back()->with('success', 'Berhasil mengupdate rincian undangan.');
    }

    public function destroyDetail(\$id)
    {
        \App\Models\UndanganDetail::findOrFail(\$id)->delete();
        \$this->recalculateRekap();
        return redirect()->back()->with('success', 'Berhasil menghapus rincian undangan.');
    }

PHP;

// Find the imports section and replace the stubs
$importsToReplace = <<<PHP
    public function import(Request \$request) {
        return redirect()->back()->with('error_modal', 'Fitur Import Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function exportExcel(Request \$request) {
        return redirect()->back()->with('error_modal', 'Fitur Export Excel Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function exportPdf(Request \$request) {
        return redirect()->back()->with('error_modal', 'Fitur Export PDF Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function downloadTemplate(Request \$request) {
        return redirect()->back()->with('error_modal', 'Template Excel Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function destroyBulkDetail(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            \Illuminate\Support\Facades\DB::table('undangan_detail')->whereIn('id', \$ids)->delete();
            return redirect()->back()->with('success', 'Berhasil menghapus data rincian secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }
PHP;

$newImports = <<<PHP
    public function import(Request \$request) {
        \$request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\UndanganImport, \$request->file('file'));
            \$this->recalculateRekap();
            return redirect()->back()->with('success', 'Data Undangan berhasil diimpor!');
        } catch (\Exception \$e) {
            \Illuminate\Support\Facades\Log::error('Import Undangan error: ' . \$e->getMessage());
            return redirect()->back()->with('error_modal', 'Gagal mengimpor data. Pastikan format file sesuai dengan template.');
        }
    }

    public function exportExcel(Request \$request) {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UndanganExport(false), 'Data_Undangan.xlsx');
    }

    public function exportPdf(Request \$request) {
        // Fallback for PDF if not fully implemented yet
        return redirect()->back()->with('error_modal', 'Fitur Export PDF Undangan sedang dalam tahap pengembangan akhir. Harap tunggu update selanjutnya.');
    }

    public function downloadTemplate(Request \$request) {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UndanganExport(true), 'Template_Import_Undangan.xlsx');
    }

    public function destroyBulkDetail(\Illuminate\Http\Request \$request)
    {
        \$ids = \$request->ids;
        if (\$ids && is_array(\$ids)) {
            \Illuminate\Support\Facades\DB::table('undangan_details')->whereIn('id', \$ids)->delete();
            \$this->recalculateRekap();
            return redirect()->back()->with('success', 'Berhasil menghapus data rincian secara massal.');
        }
        return redirect()->back()->with('error_modal', 'Tidak ada data yang dipilih.');
    }
PHP;

// Notice the DB table was wrong! It was 'undangan_detail' instead of 'undangan_details'

// Inject missing methods before import
$content = str_replace($importsToReplace, $missingMethods . "\n" . $newImports, $content);

file_put_contents($controllerPath, $content);

echo "Patched UndanganController.php\n";
