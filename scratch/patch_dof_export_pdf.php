<?php
$f = 'app/Http/Controllers/DofController.php';
$c = file_get_contents($f);

$search = "    public function exportPdf(Request \$request)
    {
        \$tahun = \$request->query('tahun', 'semua');
        \$bulan = \$request->query('bulan', 'semua');
        
        \$query = DofData::with('masterDof');
        if (\$tahun !== 'semua') \$query->where('tahun', \$tahun);
        if (\$bulan !== 'semua') \$query->where('bulan', \$bulan);
        \$data = \$query->orderBy('tahun', 'desc')->get();

        \$pdf = Pdf::loadView('exports.dof-pdf', compact('data', 'tahun', 'bulan'))
                  ->setPaper('a4', 'landscape');
        
        return \$pdf->download('Data_DOF_'.\$tahun.'_'.\$bulan.'.pdf');
    }";

$replace = "    public function exportPdf(Request \$request)
    {
        \$filterTahun = \$request->query('tahun', 'semua');
        \$filterBulan = \$request->query('bulan', 'semua');

        \$masterTabel1 = DofMaster::where('kelompok_tabel', 1)->orderBy('id', 'asc')->get();
        \$masterTabel2 = DofMaster::where('kelompok_tabel', 2)->orderBy('id', 'asc')->get();

        \$query = DofData::with('masterDof');
        if (\$filterTahun != 'semua') \$query->where('tahun', \$filterTahun);
        if (\$filterBulan != 'semua') \$query->where('bulan', \$filterBulan);
        \$rawData = \$query->get();

        \$groupedData = \$rawData->groupBy(function(\$item) { return \$item->tahun . '_' . \$item->bulan; });

        \$dataTable1 = []; \$dataTable2 = [];
        \$totalsTabel1 = array_fill_keys(\$masterTabel1->pluck('id')->toArray(), 0);
        \$totalsTabel2 = array_fill_keys(\$masterTabel2->pluck('id')->toArray(), 0);

        foreach(\$groupedData as \$key => \$items) {
            \$parts = explode('_', \$key);
            \$tahun = \$parts[0]; \$bulan = \$parts[1];

            \$rowTabel1 = ['tahun' => \$tahun, 'bulan' => \$bulan, 'items' => [], 'data_tambahan' => []];
            \$rowTabel2 = ['tahun' => \$tahun, 'bulan' => \$bulan, 'items' => [], 'data_tambahan' => []];

            foreach(\$items as \$item) {
                \$master = \$item->masterDof;
                if (\$master->kelompok_tabel == 1) {
                    \$rowTabel1['items'][\$master->id] = \$item->jumlah;
                    \$totalsTabel1[\$master->id] += \$item->jumlah;
                    if (!empty(\$item->data_tambahan)) \$rowTabel1['data_tambahan'] = array_merge(\$rowTabel1['data_tambahan'], \$item->data_tambahan);
                } else {
                    \$rowTabel2['items'][\$master->id] = \$item->jumlah;
                    \$totalsTabel2[\$master->id] += \$item->jumlah;
                    if (!empty(\$item->data_tambahan)) \$rowTabel2['data_tambahan'] = array_merge(\$rowTabel2['data_tambahan'], \$item->data_tambahan);
                }
            }
            if (count(\$rowTabel1['items']) > 0) \$dataTable1[] = \$rowTabel1;
            if (count(\$rowTabel2['items']) > 0) \$dataTable2[] = \$rowTabel2;
        }

        \$monthsOrder = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        \$sorter = function(\$a, \$b) use (\$monthsOrder) {
            if(\$a['tahun'] == \$b['tahun']) return \$monthsOrder[\$b['bulan']] <=> \$monthsOrder[\$a['bulan']];
            return \$b['tahun'] <=> \$a['tahun'];
        };
        usort(\$dataTable1, \$sorter);
        usort(\$dataTable2, \$sorter);

        \$kolomTabel1 = DB::table('dynamic_columns')->where('modul', 'dof_1')->get();
        \$kolomTabel2 = DB::table('dynamic_columns')->where('modul', 'dof_2')->get();

        \$pdf = Pdf::loadView('pdf.dof', compact(
            'filterTahun', 'filterBulan', 'masterTabel1', 'masterTabel2', 
            'dataTable1', 'dataTable2', 'totalsTabel1', 'totalsTabel2',
            'kolomTabel1', 'kolomTabel2'
        ))->setPaper('a4', 'landscape');
        
        return \$pdf->download('Laporan_DOF_'.\$filterTahun.'_'.\$filterBulan.'.pdf');
    }";

$c = str_replace($search, $replace, $c);
file_put_contents($f, $c);
echo "Patched DofController exportPdf\n";
