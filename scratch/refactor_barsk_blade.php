<?php
$file = 'd:\web_pkt_adkor_internal\adkor-report-internal\resources\views\bar-sk-memo.blade.php';
$content = file_get_contents($file);

// Refactor Terbit table
$searchTerbit1 = '@forelse($rawData as $row)
                    @if($row->skd_keputusan_bersama_terbit || $row->skd_non_ratifikasi_terbit || $row->skd_ratifikasi_terbit || $row->memo_direksi_terbit || $row->bar_monitoring_terbit || $row->bar_manajemen_terbit || ($row->data_tambahan ?? null))';
$replaceTerbit1 = '@forelse($dataTerbit as $row)';
$content = str_replace($searchTerbit1, $replaceTerbit1, $content);

// Remove the matching @endif for Terbit
// We need to carefully remove it.
$content = preg_replace('/(<\/td>\s*<\/tr>\s*)@endif(\s*@empty)/', '$1$2', $content);

// Refactor Proses table
$searchProses1 = '@forelse($rawData as $row)
                    @if($row->proses_skd_keputusan_bersama || $row->proses_skd_non_ratifikasi || $row->proses_skd_ratifikasi || $row->proses_memo_direksi || $row->proses_bar_monitoring || $row->proses_bar_manajemen || ($row->data_tambahan_proses ?? null))';
$replaceProses1 = '@forelse($dataProses as $row)';
$content = str_replace($searchProses1, $replaceProses1, $content);

// Add Pagination links at the end of each x-table
$searchTableEndTerbit = '              </x-table>';
// Wait, both tables have </x-table>. We should inject links right after them.
// Let's replace the first </x-table> with Terbit links, and the second with Proses links.

$parts = explode('</x-table>', $content);
if (count($parts) >= 3) {
    // There should be 2 tables.
    // parts[0] is before Terbit </x-table>
    // parts[1] is after Terbit </x-table> and before Proses </x-table>
    // parts[2] is after Proses </x-table>
    
    $content = $parts[0] . '</x-table>
              <div class="mt-4 px-4 pb-4">
                  {{ $dataTerbit->links() }}
              </div>' . $parts[1] . '</x-table>
              <div class="mt-4 px-4 pb-4">
                  {{ $dataProses->links() }}
              </div>' . $parts[2];
}

file_put_contents($file, $content);
echo "Blade template refactored\n";
