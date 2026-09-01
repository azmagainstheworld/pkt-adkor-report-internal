<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ==========================================
    // JS HAPUS MASSAL (BULK DELETE)
    // ==========================================
    function toggleBulkMode(tipe) {
        let container = document.getElementById("tableContainer" + (tipe === "terbit" ? "Terbit" : "Proses"));
        if(container.classList.contains("hide-bulk-" + tipe)) {
            container.classList.remove("hide-bulk-" + tipe);
        } else {
            container.classList.add("hide-bulk-" + tipe);
            cancelAll(tipe);
        }
    }
    
    function toggleSelectAll(tipe) {
        let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
        let checkboxes = document.querySelectorAll(".cb-" + tipe);
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        toggleDeleteBtn(tipe);
    }
    
    function toggleCheckbox(tipe) {
        let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
        let checkboxes = document.querySelectorAll(".cb-" + tipe);
        selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
        toggleDeleteBtn(tipe);
    }
    
    function toggleDeleteBtn(tipe) {
        let group = document.getElementById("btnGroup" + (tipe === "terbit" ? "Terbit" : "Proses"));
        if(group) {
            let checked = document.querySelectorAll(".cb-" + tipe + ":checked").length > 0;
            if(checked) group.classList.remove("hidden"); else group.classList.add("hidden");
        }
    }
    
    function cancelAll(tipe) {
        let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
        if(selectAll) selectAll.checked = false;
        let checkboxes = document.querySelectorAll(".cb-" + tipe);
        checkboxes.forEach(cb => cb.checked = false);
        toggleDeleteBtn(tipe);
    }

    // ==========================================
    // JS MODAL & UI HELPERS
    // ==========================================
    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    
    function toggleDropdown(id) {
        const el = document.getElementById(id);
        const isHidden = el.classList.contains('hidden');
        document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        if (isHidden) el.classList.remove('hidden');
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative.inline-block')) {
            document.querySelectorAll('[id^="dropdown"]').forEach(drop => drop.classList.add('hidden'));
        }
    });

    function triggerDeleteKolom(modalAsal, deleteUrl) {
        closeModal(modalAsal);
        setTimeout(() => { openDeleteModal('modalHapusKolom', deleteUrl); }, 200);
    }

    function toggleDropdownConfig(selectorId, configAreaId) {
        const selector = document.getElementById(selectorId);
        const configArea = document.getElementById(configAreaId);
        if (selector.value === 'dropdown') {
            configArea.classList.remove('hidden');
            configArea.querySelector('input').setAttribute('required', 'true');
        } else {
            configArea.classList.add('hidden');
            configArea.querySelector('input').removeAttribute('required');
        }
    }

    // JS SIHIR: Hapus angka 0 di depan saat input
    document.addEventListener('input', function(e) {
        if (e.target && e.target.type === 'number') {
            let val = e.target.value;
            if (val.length > 1 && val.startsWith('0')) {
                e.target.value = val.replace(/^0+/, '');
                if (e.target.value === '') e.target.value = '0';
            }
        }
        if(e.target && e.target.classList.contains('input-currency')) {
            let value = e.target.value.replace(/[^,\d]/g, '');
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if(ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
            e.target.value = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }
    });

    const namaBulanIndo = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    function syncPeriode(val, yearId, monthId) {
        if(val) {
            const parts = val.split('-');
            document.getElementById(yearId).value = parts[0];
            document.getElementById(monthId).value = namaBulanIndo[parseInt(parts[1], 10) - 1];
        }
    }
    function getMonthPickerValue(tahun, bulanName) {
        const monthIndex = namaBulanIndo.indexOf(bulanName);
        if(monthIndex > -1) { return `${tahun}-${String(monthIndex + 1).padStart(2, '0')}`; } return '';
    }

    function openModalTambah(tipe) {
        const now = new Date();
        const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        if(tipe === 'Terbit') {
            document.getElementById('picker_t_terbit').value = currentMonth; syncPeriode(currentMonth, 'tt_thn', 'tt_bln');
            openModal('modalTambahTerbit');
        } else {
            document.getElementById('picker_t_proses').value = currentMonth; syncPeriode(currentMonth, 'tp_thn', 'tp_bln');
            openModal('modalTambahProses');
        }
    }

    function editTerbit(row) {
        const pickerVal = getMonthPickerValue(row.tahun, row.bulan);
        document.getElementById('picker_t_terbit').value = pickerVal; syncPeriode(pickerVal, 'tt_thn', 'tt_bln');
        document.querySelector('#modalTambahTerbit input[name="skd_kb"]').value = row.skd_keputusan_bersama_terbit;
        document.querySelector('#modalTambahTerbit input[name="skd_nr"]').value = row.skd_non_ratifikasi_terbit;
        document.querySelector('#modalTambahTerbit input[name="skd_r"]').value = row.skd_ratifikasi_terbit;
        document.querySelector('#modalTambahTerbit input[name="memo"]').value = row.memo_direksi_terbit;
        document.querySelector('#modalTambahTerbit input[name="bar_mon"]').value = row.bar_monitoring_terbit;
        document.querySelector('#modalTambahTerbit input[name="bar_man"]').value = row.bar_manajemen_terbit;

        // Auto-fill JSON tambahan terbit
        const tambahan = typeof row.data_tambahan === 'string' ? JSON.parse(row.data_tambahan) : (row.data_tambahan || {});
        document.querySelectorAll('.input-dinamis-terbit').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });

        openModal('modalTambahTerbit');
    }

    function editProses(row) {
        const pickerVal = getMonthPickerValue(row.tahun, row.bulan);
        document.getElementById('picker_t_proses').value = pickerVal; syncPeriode(pickerVal, 'tp_thn', 'tp_bln');
        document.querySelector('#modalTambahProses input[name="skd_kb"]').value = row.proses_skd_keputusan_bersama;
        document.querySelector('#modalTambahProses input[name="skd_nr"]').value = row.proses_skd_non_ratifikasi;
        document.querySelector('#modalTambahProses input[name="skd_r"]').value = row.proses_skd_ratifikasi;
        document.querySelector('#modalTambahProses input[name="memo"]').value = row.proses_memo_direksi;
        document.querySelector('#modalTambahProses input[name="bar_mon"]').value = row.proses_bar_monitoring;
        document.querySelector('#modalTambahProses input[name="bar_man"]').value = row.proses_bar_manajemen;

        // Auto-fill JSON tambahan proses
        const tambahan = typeof row.data_tambahan_proses === 'string' ? JSON.parse(row.data_tambahan_proses) : (row.data_tambahan_proses || {});
        document.querySelectorAll('.input-dinamis-proses').forEach(el => {
            const key = el.getAttribute('data-key');
            el.value = (tambahan && tambahan[key] !== undefined) ? tambahan[key] : '';
        });

        openModal('modalTambahProses');
    }

    // ================= Helper Init Chart =================
    const colors = {!! json_encode($chartColors) !!};
    window.chartDataMap = {};

    function createChart(canvasId, rawData, keys, labels, type = 'bar') {
        const canvas = document.getElementById('canvas_' + canvasId);
        if (!canvas) return null;
        const ctx = canvas.getContext('2d');
        const isBar = type === 'bar';
        const isCircular = (type === 'pie' || type === 'doughnut');

        let chartData;
        let options = {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        };

        if (isCircular) {
            const totals = labels.map(lbl => rawData.reduce((sum, d) => sum + (d.items[lbl] || 0), 0));
            chartData = {
                labels: labels,
                datasets: [{
                    data: totals,
                    backgroundColor: labels.map((_, i) => colors[i % colors.length]),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            };
        } else {
            options.interaction = { mode: 'index', intersect: false };
            options.scales = {
                y: { stacked: true, beginAtZero: true, grid: { color: '#F3F4F6', drawBorder: false } },
                x: { stacked: true, grid: { display: false, drawBorder: false } }
            };
            chartData = {
                labels: rawData.map(d => d.label),
                datasets: keys.map((key, index) => ({
                    label: labels[index],
                    data: rawData.map(d => d.items[labels[index]] || 0),
                    backgroundColor: colors[index % colors.length],
                    stack: 'Stack 0',
                    borderRadius: 4
                }))
            };
        }

        return new Chart(ctx, { type: type, data: chartData, options: options });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const rawDataTerbit = {!! json_encode($chartDataTerbit) !!};
        const rawDataProses = {!! json_encode($chartDataProses) !!};
        
        const keyTerbit = ['bar_mon', 'memo', 'skd_kb', 'skd_nr', 'skd_r', 'bar_man'];
        const lblTerbit = ['BAR Monitoring', 'Memo Direksi', 'SKD Keputusan Bersama', 'SKD Non Ratifikasi', 'SKD Ratifikasi', 'BAR Manajemen'];
        
        const keyProses = ['p_bar_mon', 'p_memo', 'p_skd_kb', 'p_skd_nr', 'p_skd_r', 'p_bar_man'];
        const lblProses = ['Proses BAR Monitoring', 'Proses Memo Direksi', 'Proses SKD Keputusan Bersama', 'Proses SKD Non Ratifikasi', 'Proses SKD Ratifikasi', 'Proses BAR Manajemen'];

        window.chartDataMap['chartTerbit'] = { raw: rawDataTerbit, keys: keyTerbit, labels: lblTerbit };
        window.chartDataMap['chartProses'] = { raw: rawDataProses, keys: keyProses, labels: lblProses };

        window.chart_chartTerbit = createChart('chartTerbit', rawDataTerbit, keyTerbit, lblTerbit, 'bar');
        window.chart_chartProses = createChart('chartProses', rawDataProses, keyProses, lblProses, 'bar');
    });

    function changeChartType(id, type) {
        try { if (window['chart_' + id]) { window['chart_' + id].destroy(); } } catch (e) {}
        window['chart_' + id] = null;
        const map = window.chartDataMap[id];
        if (map) { window['chart_' + id] = createChart(id, map.raw, map.keys, map.labels, type); }
    }
</script>
