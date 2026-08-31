<?php
$file = 'resources/views/program-strategis.blade.php';
$content = file_get_contents($file);

// Task 2: Fix Kendala being 'In Progress' because in the past I set 'kendala' => '-' if empty, but for some reason it was corrupted. I'll just write a quick DB fix below.

// Task 4: Edit modal -> Sasaran & Program Strategis make readonly
$search3 = '<select name="sasaran_program_select" id="edit_sp_select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none cursor-pointer">';
$replace3 = '<select name="sasaran_program_select" id="edit_sp_select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm bg-gray-100 outline-none cursor-not-allowed" style="pointer-events: none;" tabindex="-1" readonly>';
$content = str_replace($search3, $replace3, $content);

// Task 4: modalTambah (Tambah Rincian Kegiatan) -> Add TARGET WAKTU, KENDALA, KETERANGAN, STATUS
$search4 = '              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Progress Saat Ini</label>
                      <textarea name="progress_saat_ini" id="add_progress" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                  </div>';

$replace4 = '              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  
                  <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Kegiatan <span class="text-red-500">*</span></label>
                      <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none cursor-pointer">
                          <option value="-">- (Belum Dimulai)</option>
                          <option value="In Progress">In Progress</option>
                          <option value="Selesai">Selesai</option>
                          <option value="Hold">Hold</option>
                          <option value="Tercapai">Tercapai</option>
                          <option value="Berjalan">Berjalan</option>
                          <option value="Tertunda">Tertunda</option>
                      </select>
                  </div>

                  <div class="col-span-1">
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Waktu (Mulai)</label>
                      <input type="date" name="target_waktu_start" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                  </div>
                  <div class="col-span-1">
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Target Waktu (Selesai)</label>
                      <input type="date" name="target_waktu_end" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                  </div>

                  <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Progress Saat Ini</label>
                      <textarea name="progress_saat_ini" id="add_progress" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                  </div>
                  
                  <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Kendala</label>
                      <textarea name="kendala" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none"></textarea>
                  </div>
                  
                  <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan Tambahan</label>
                      <input type="text" name="keterangan_tambahan" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-orange-500 outline-none">
                  </div>';
                  
$content = str_replace($search4, $replace4, $content);
file_put_contents($file, $content);
echo "Blade file patched.\n";

// DB Fix for kendala
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\ProgramStrategis::whereIn('kendala', ['In Progress', 'Selesai', 'Hold', 'Berjalan'])->update(['kendala' => '-']);
echo "DB Kendala fixed.\n";
