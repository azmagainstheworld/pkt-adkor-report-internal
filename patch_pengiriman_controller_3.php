<?php

$filePath = 'app/Http/Controllers/PengirimanDokumenController.php';
$content = file_get_contents($filePath);

// Fix index() searching and overall totals
$indexOld = <<<PHP
        if (\$request->filled('search')) {
            \$search = \$request->search;
            \$baseQuery->where(function(\$q) use (\$search) {
                \$q->where('tahun', 'like', "%{\$search}%")->orWhere('bulan', 'like', "%{\$search}%")
                  ->orWhere('penerimaan_mailroom', 'like', "%{\$search}%")->orWhere('pengiriman_dalam_negeri', 'like', "%{\$search}%")
                  ->orWhere('pengiriman_luar_negeri', 'like', "%{\$search}%")->orWhere('registrasi_surat_masuk_dof', 'like', "%{\$search}%")
                  ->orWhere('ongkir_dalam_negeri', 'like', "%{\$search}%")->orWhere('ongkir_luar_negeri', 'like', "%{\$search}%");
            });
        }
PHP;
$indexNew = <<<PHP
        if (\$request->filled('search')) {
            \$search = \$request->search;
            \$baseQuery->where(function(\$q) use (\$search) {
                \$q->where('tahun', 'like', "%{\$search}%")->orWhere('bulan', 'like', "%{\$search}%")
                  ->orWhere('penerimaan_mailroom', 'like', "%{\$search}%")->orWhere('pengiriman_dalam_negeri', 'like', "%{\$search}%")
                  ->orWhere('pengiriman_luar_negeri', 'like', "%{\$search}%")->orWhere('registrasi_surat_masuk_dof', 'like', "%{\$search}%");
            });
        }
PHP;
$content = str_replace($indexOld, $indexNew, $content);

$totalsOld = <<<PHP
        \$totalDomestikOverall = (clone \$baseQuery)->sum('ongkir_dalam_negeri');
        \$totalInternasionalOverall = (clone \$baseQuery)->sum('ongkir_luar_negeri');
PHP;
$totalsNew = <<<PHP
        \$ongkirBaseQuery = \App\Models\PengirimanOngkir::query();
        if (\$selectedYear != 'semua') \$ongkirBaseQuery->where('tahun', \$selectedYear);
        if (\$selectedMonth != 'semua') \$ongkirBaseQuery->where('bulan', \$selectedMonth);
        if (\$request->filled('search')) {
            \$search = \$request->search;
            \$ongkirBaseQuery->where(function(\$q) use (\$search) {
                \$q->where('tahun', 'like', "%{\$search}%")->orWhere('bulan', 'like', "%{\$search}%")
                  ->orWhere('ongkir_dalam_negeri', 'like', "%{\$search}%")->orWhere('ongkir_luar_negeri', 'like', "%{\$search}%");
            });
        }
        
        \$totalDomestikOverall = (clone \$ongkirBaseQuery)->sum('ongkir_dalam_negeri');
        \$totalInternasionalOverall = (clone \$ongkirBaseQuery)->sum('ongkir_luar_negeri');
PHP;
$content = str_replace($totalsOld, $totalsNew, $content);

// Fix exportPdf()
$exportPdfOld = <<<PHP
    public function exportPdf(Request \$request)
    {
        \$year = \$request->input('year', 'semua');
        \$month = \$request->input('month', 'semua');

        \$query = PengirimanDokumen::query();
        if (\$year !== 'semua') \$query->where('tahun', \$year);
        if (\$month !== 'semua') \$query->where('bulan', \$month);

        \$query->orderBy('tahun', 'desc')->orderByRaw("FIELD(bulan, '" . implode("','", \$this->masterMonths) . "')");
        \$records = \$query->get();

        \$totalDomestik = \$records->sum('ongkir_dalam_negeri');
        \$totalInternasional = \$records->sum('ongkir_luar_negeri');
PHP;
$exportPdfNew = <<<PHP
    public function exportPdf(Request \$request)
    {
        \$year = \$request->input('year', 'semua');
        \$month = \$request->input('month', 'semua');
        \$jenis = \$request->input('jenis', 'volume');

        \$query = \$jenis === 'ongkir' ? \App\Models\PengirimanOngkir::query() : PengirimanDokumen::query();
        if (\$year !== 'semua') \$query->where('tahun', \$year);
        if (\$month !== 'semua') \$query->where('bulan', \$month);

        \$query->orderBy('tahun', 'desc')->orderByRaw("FIELD(bulan, '" . implode("','", \$this->masterMonths) . "')");
        \$records = \$query->get();

        \$totalDomestik = \$jenis === 'ongkir' ? \$records->sum('ongkir_dalam_negeri') : 0;
        \$totalInternasional = \$jenis === 'ongkir' ? \$records->sum('ongkir_luar_negeri') : 0;
PHP;
$content = str_replace($exportPdfOld, $exportPdfNew, $content);

file_put_contents($filePath, $content);
echo "PengirimanDokumenController updated for aggregate queries.\n";
