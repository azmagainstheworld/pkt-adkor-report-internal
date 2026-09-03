<?php

$file = 'resources/views/pemeliharaan.blade.php';
$content = file_get_contents($file);

$missingFunctions = <<<'EOD'
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    function toggleDropdown(id) { document.getElementById(id).classList.toggle('hidden'); }
    document.addEventListener('click', function(event) { if (!event.target.closest('.relative.inline-block')) { document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden')); } });

    function triggerDeleteKolom(deleteUrl, modalId) { closeModal(modalId); setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200); }
    function toggleDropdownConfig(type) {
        const selector = document.getElementById('tipeInputSelector' + type);
        const configArea = document.getElementById('dropdownConfigArea' + type);
        if(selector.value === 'dropdown') { configArea.classList.remove('hidden'); configArea.querySelector('input').setAttribute('required', 'true'); } 
        else { configArea.classList.add('hidden'); configArea.querySelector('input').removeAttribute('required'); }
    }

    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
        } else {
            document.getElementById(yearId).value = '';
            document.getElementById(monthId).value = '';
        }
    }

    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) { return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`; } return '';
    }

    function openModalTambahRutin() { 
        const countData = {{ count($masterRutin) }}; if (countData === 0) { alert('Silakan atur Dokumen Kegiatan terlebih dahulu melalui Opsi Lanjutan.'); return; }
        const now = new Date(); const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('picker_tambah_rutin').value = currentMonth; syncPeriode(currentMonth, 'rutin_tahun', 'rutin_bulan');
        openModal('modalTambahRutin'); 
    }
    
EOD;

$content = str_replace("    function openModalTambahPeralatan() {", $missingFunctions . "    function openModalTambahPeralatan() {", $content);

file_put_contents($file, $content);
echo "Fixed!\n";
