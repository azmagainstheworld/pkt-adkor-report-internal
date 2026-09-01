# Catatan & Panduan Logika Fitur Hapus Massal (Bulk Delete / Select All)

Dokumen ini berisi penjelasan lengkap alur kerja, logika JavaScript, struktur HTML, serta penanganan Backend (Laravel) untuk fitur **Hapus Massal (Bulk Delete)** dengan fungsi **Select All**.

---

## 1. Konsep Utama & Alur Kerja (Workflow)

Secara garis besar, fitur Hapus Massal bekerja melalui 3 lapisan utama:

```mermaid
graph TD
    A[User Klik 'Mode Hapus Massal'] --> B[JS Toggle Class CSS untuk Tampilkan Checkbox]
    B --> C[User Klik Checkbox 'Select All' atau Checkbox Baris]
    C --> D[JS Update State Checkbox & Tampilkan Tombol Hapus]
    D --> E[User Klik 'Hapus Terpilih' & Konfirmasi]
    E --> F[Submit Form POST dengan Array IDs & _method DELETE]
    F --> G[Laravel Controller: BarSkMemoBulanan::whereIn('id', $ids)->delete()]
    G --> H[Redirect Back dengan Pesan Sukses]
```

---

## 2. Struktur HTML & Blade Template

### A. CSS Penyembunyi Checkbox Default
Secara default, kolom checkbox disembunyikan menggunakan CSS agar tampilan tabel tetap rapi jika mode hapus massal belum diaktifkan:

```html
<style>
/* Kolom pertama (checkbox) disembunyikan jika class hide-bulk aktif */
.hide-bulk-terbit th:first-child, .hide-bulk-terbit td:first-child { display: none !important; }
.hide-bulk-proses th:first-child, .hide-bulk-proses td:first-child { display: none !important; }
</style>
```

### B. Tombol Aktivasi Mode Hapus Massal
```html
<button type="button" id="btnModeBulkTerbit" onclick="toggleBulkMode('terbit')" class="...">
    Mode Hapus Massal
</button>
```

### C. Pembungkus Form & Tabel
Seluruh baris data dan checkbox dibungkus dalam satu `<form>` utama:

```html
<form id="bulkDeleteTerbitForm" action="{{ route('bar-sk-memo.destroyTerbit') }}" method="POST" onsubmit="return confirm('Hapus data terpilih?')">
    @csrf
    @method('DELETE')
    
    <!-- Floating / Top Action Bar untuk Tombol Hapus -->
    <div id="btnGroupTerbit" class="hidden flex justify-between items-center px-4 py-2 bg-red-50">
        <span class="text-xs text-red-600 font-semibold">Data terpilih untuk dihapus</span>
        <div class="flex gap-2">
            <button type="button" onclick="cancelAll('terbit')" class="...">Batal</button>
            <button type="submit" class="...">Hapus Terpilih</button>
        </div>
    </div>

    <!-- Container Tabel dengan class hide-bulk-terbit -->
    <div id="tableContainerTerbit" class="hide-bulk-terbit overflow-x-auto">
        <x-table :headers="$headTerbit">
            @foreach($dataTerbit as $row)
                <tr>
                    <!-- Checkbox per Baris dengan Name Array ids[] -->
                    <td class="text-center">
                        <input type="checkbox" name="ids[]" class="cb-terbit" value="{{ $row->id }}" onclick="toggleCheckbox('terbit')">
                    </td>
                    ...
                </tr>
            @endforeach
        </x-table>
    </div>
</form>
```

---

## 3. Logika JavaScript (Client-Side)

Logika JavaScript mengontrol interaksi dinamis di browser:

```javascript
// 1. Mengaktifkan / Mematikan Mode Hapus Massal
function toggleBulkMode(tipe) {
    let container = document.getElementById("tableContainer" + (tipe === "terbit" ? "Terbit" : "Proses"));
    if (container.classList.contains("hide-bulk-" + tipe)) {
        // Tampilkan kolom checkbox
        container.classList.remove("hide-bulk-" + tipe);
    } else {
        // Sembunyikan kolom checkbox & reset semua centangan
        container.classList.add("hide-bulk-" + tipe);
        cancelAll(tipe);
    }
}

// 2. Logika Checkbox "Select All" di Header Tabel
function toggleSelectAll(tipe) {
    let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
    let checkboxes = document.querySelectorAll(".cb-" + tipe);
    
    // Setel centang pada seluruh checkbox baris sesuai dengan checkbox header
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    
    // Perbarui status tombol Hapus
    toggleDeleteBtn(tipe);
}

// 3. Logika Checkbox Baris Individu
function toggleCheckbox(tipe) {
    let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
    let checkboxes = document.querySelectorAll(".cb-" + tipe);
    
    // Jika SELURUH checkbox baris tercentang, maka Select All otomatis tercentang
    selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
    
    // Perbarui status tombol Hapus
    toggleDeleteBtn(tipe);
}

// 4. Menampilkan / Menyembunyikan Tombol "Hapus Terpilih"
function toggleDeleteBtn(tipe) {
    let group = document.getElementById("btnGroup" + (tipe === "terbit" ? "Terbit" : "Proses"));
    if (group) {
        // Cek apakah minimal ada 1 checkbox yang tercentang
        let checked = document.querySelectorAll(".cb-" + tipe + ":checked").length > 0;
        if (checked) {
            group.classList.remove("hidden");
        } else {
            group.classList.add("hidden");
        }
    }
}

// 5. Membatalkan / Reset Semua Centangan
function cancelAll(tipe) {
    let selectAll = document.getElementById("selectAll" + (tipe === "terbit" ? "Terbit" : "Proses"));
    if (selectAll) selectAll.checked = false;
    
    let checkboxes = document.querySelectorAll(".cb-" + tipe);
    checkboxes.forEach(cb => cb.checked = false);
    
    toggleDeleteBtn(tipe);
}
```

---

## 4. Logika Backend (Laravel Controller & Route)

### A. Routing (`routes/web.php`)
```php
Route::delete('/bar-sk-memo/destroy-terbit', [BarSkMemoController::class, 'destroyTerbit'])->name('bar-sk-memo.destroyTerbit');
Route::delete('/bar-sk-memo.destroy-proses', [BarSkMemoController::class, 'destroyProses'])->name('bar-sk-memo.destroyProses');
```

### B. Controller Method (`BarSkMemoController.php`)
```php
public function destroyTerbit(Request $request)
{
    // 1. Hapus tunggal (jika dipanggil dari tombol hapus individual di kolom Aksi)
    if ($request->has('id')) {
        BarSkMemoBulanan::where('id', $request->id)->delete();
        return redirect()->back()->with('success', 'Data terbit berhasil dihapus.');
    }

    // 2. Hapus Massal (Bulk Delete dari array ids[])
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'exists:bar_sk_memo,id',
    ]);

    // Eksekusi penghapusan massal dalam satu query efisien
    BarSkMemoBulanan::whereIn('id', $request->ids)->delete();

    return redirect()->back()->with('success', count($request->ids) . ' Data terbit berhasil dihapus.');
}
```

---

## 5. Ringkasan Poin Penting (Best Practices)

1. **Efisiensi Database**: Menggunakan `whereIn('id', $request->ids)->delete()` jauh lebih cepat dibanding looping `delete()` satu-per-satu karena hanya menjalankan 1 SQL `DELETE FROM table WHERE id IN (...)`.
2. **Validasi Keamanan**:
   - Sertakan `@csrf` dan `@method('DELETE')` untuk mencegah serangan CSRF.
   - Validasi `ids` sebagai `array` wajib di Controller.
3. **UX (User Experience)**:
   - Tambahkan konfirmasi browser `onsubmit="return confirm(...)"` agar user tidak sengaja menghapus data.
   - Sembunyikan tombol hapus saat belum ada data yang tercentang.
