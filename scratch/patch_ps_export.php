<?php
$file = 'app/Exports/ProgramStrategisExport.php';
$content = file_get_contents($file);

// Replace collection() sorting
$search1 = "return \$query->orderBy('tahun', 'desc')->orderBy('sasaran', 'asc')->orderBy('program_strategis', 'asc')->get();";
$replace1 = "return \$query->orderBy('tahun', 'desc')->orderBy('id', 'asc')->get();";
$content = str_replace($search1, $replace1, $content);

// Replace map() logic
$search2 = '    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        // Pakai Array Bahasa Indonesia Anti-Error
        $bulanStr = ($row->bulan && is_numeric($row->bulan)) ? $this->bulanIndo[(int)$row->bulan] : \'-\';

        $mapped = [
            $row->tahun,
            $bulanStr,
            $row->sasaran,
            $row->program_strategis,
            $row->deskripsi_kegiatan, // Tetap ngambil dari database
            $row->target_waktu_start,
            $row->target_waktu_end,
            $row->realisasi,
            $row->progress_saat_ini,
            $row->kendala,
            $row->keterangan_tambahan,
            $row->status,
        ];';

$replace2 = '    protected $lastParentKey = null;

    public function map($row): array
    {
        $tambahan = is_string($row->data_tambahan) ? json_decode($row->data_tambahan, true) : ($row->data_tambahan ?? []);

        // Pakai Array Bahasa Indonesia Anti-Error
        $bulanStr = ($row->bulan && is_numeric($row->bulan)) ? $this->bulanIndo[(int)$row->bulan] : \'-\';

        $currentKey = $row->tahun . \'_\' . $row->bulan . \'_\' . $row->sasaran . \'_\' . $row->program_strategis;
        $isFirst = ($this->lastParentKey !== $currentKey);
        $this->lastParentKey = $currentKey;

        $mapped = [
            $isFirst ? $row->tahun : \'\',
            $isFirst ? $bulanStr : \'\',
            $isFirst ? $row->sasaran : \'\',
            $isFirst ? $row->program_strategis : \'\',
            $row->deskripsi_kegiatan, // Tetap ngambil dari database
            $isFirst ? $row->target_waktu_start : \'\',
            $isFirst ? $row->target_waktu_end : \'\',
            $row->realisasi,
            $row->progress_saat_ini,
            $isFirst ? $row->kendala : \'\',
            $isFirst ? $row->keterangan_tambahan : \'\',
            $isFirst ? $row->status : \'\',
        ];';

$content = str_replace($search2, $replace2, $content);
file_put_contents($file, $content);
echo "ProgramStrategisExport patched.\n";
