<?php
$f = 'resources/views/perizinan-perkantoran.blade.php';
$c = file_get_contents($f);

// 1. Add IDs
$c = preg_replace('/<!-- ================= TABEL 1: RINGKASAN ================= -->\s*<x-card class="!rounded-xl !p-0 shadow-sm border border-gray-100 mb-8 bg-white">/', '<!-- ================= TABEL 1: RINGKASAN ================= -->
    <x-card id="tabel-ringkasan" class="!rounded-xl !p-0 shadow-sm border border-gray-100 mb-8 bg-white">', $c);

$c = preg_replace('/<!-- ================= TABEL 2: RINCIAN PERIZINAN TERBIT ================= -->\s*<x-card class="!rounded-xl !p-0 shadow-sm border border-gray-100 mb-8 bg-white relative">/', '<!-- ================= TABEL 2: RINCIAN PERIZINAN TERBIT ================= -->
    <x-card id="tabel-terbit" class="!rounded-xl !p-0 shadow-sm border border-gray-100 mb-8 bg-white relative">', $c);

$c = preg_replace('/<!-- ================= TABEL 3: PERIZINAN PROSES ================= -->\s*<x-card class="!rounded-xl !p-0 shadow-sm border border-gray-100 mb-8 bg-white relative">/', '<!-- ================= TABEL 3: PERIZINAN PROSES ================= -->
    <x-card id="tabel-proses" class="!rounded-xl !p-0 shadow-sm border border-gray-100 mb-8 bg-white relative">', $c);

// 2. Fix pagination number
$c = str_replace('@php $no = 1; @endphp', '@php $no = $dataProses->firstItem(); @endphp', $c);

// 3. Fix date formats
$oldDates = <<<'EOD'
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-center align-middle">{{ Carbon\Carbon::parse($rincian->tanggal_sejak)->translatedFormat('d F Y') }}</td>
                        <td class="px-6 py-4 text-red-500 font-medium whitespace-nowrap text-center align-middle">{{ Carbon\Carbon::parse($rincian->tanggal_akhir)->translatedFormat('d F Y') }}</td>
                        <td class="px-6 py-4 text-gray-600 truncate max-w-[150px] text-center align-middle" title="{{ $rincian->instansi_penerbit }}">{{ $rincian->instansi_penerbit }}</td>
                        <td class="px-6 py-4 text-gray-600 font-medium bg-red-50/50 text-center align-middle">{{ Carbon\Carbon::parse($rincian->tanggal_sejak)->translatedFormat('F') }}</td>
EOD;

$newDates = <<<'EOD'
                        @php
                            $bulanPendek = [
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                                7 => 'Jul', 8 => 'Agustus', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                            ];
                            $tglSejak = Carbon\Carbon::parse($rincian->tanggal_sejak);
                            $formatSejak = $tglSejak->format('d-') . $bulanPendek[$tglSejak->month] . $tglSejak->format('-y');
                            
                            $tglAkhir = Carbon\Carbon::parse($rincian->tanggal_akhir);
                            $formatAkhir = $tglAkhir->format('d-') . $bulanPendek[$tglAkhir->month] . $tglAkhir->format('-y');
                        @endphp
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap text-center align-middle">{{ $formatSejak }}</td>
                        <td class="px-6 py-4 text-red-500 font-medium whitespace-nowrap text-center align-middle">{{ $formatAkhir }}</td>
                        <td class="px-6 py-4 text-gray-600 truncate max-w-[150px] text-center align-middle" title="{{ $rincian->instansi_penerbit }}">{{ $rincian->instansi_penerbit }}</td>
                        <td class="px-6 py-4 text-gray-600 font-medium bg-red-50/50 text-center align-middle">
                            @php
                                $bulanPanjang = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            {{ $bulanPanjang[$tglSejak->month] }}
                        </td>
EOD;

$c = str_replace($oldDates, $newDates, $c);

// 4. Add highlight JS
$js = <<<'EOD'
@if(request('search'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchTerm = {!! json_encode(request('search')) !!}.toLowerCase();
        const root = document.querySelector('main');
        if (root) {
            const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
            let node;
            const nodesToReplace = [];
            
            while (node = walker.nextNode()) {
                if (node.nodeValue.toLowerCase().includes(searchTerm) && node.parentNode.nodeName !== 'SCRIPT' && node.parentNode.nodeName !== 'STYLE') {
                    nodesToReplace.push(node);
                }
            }
            
            let firstMark = null;
            nodesToReplace.forEach(n => {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                const span = document.createElement('span');
                span.innerHTML = n.nodeValue.replace(regex, '<mark class="bg-yellow-300 text-black px-1 rounded font-semibold">$1</mark>');
                n.parentNode.replaceChild(span, n);
                if (!firstMark) firstMark = span.querySelector('mark');
            });
            
            if (firstMark) {
                firstMark.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
</script>
@endif

<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
EOD;

$c = str_replace('<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">', $js, $c);

file_put_contents($f, $c);
echo "Reapplied successfully.\n";
