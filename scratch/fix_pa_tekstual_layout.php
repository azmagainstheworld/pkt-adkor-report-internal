<?php

$file = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$content = file_get_contents($file);

// Fix Tabel 1 end
$tabel1_bad = <<<BLADE
            </x-table>
                </div>
        </form>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ \$paginatedTable1->links('pagination::tailwind') }}
        </div>
        </div>
    </x-card>
BLADE;

$tabel1_good = <<<BLADE
            </x-table>
            </div>
        </form>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ \$paginatedTable1->links('pagination::tailwind') }}
        </div>
    </x-card>
BLADE;

$content = str_replace($tabel1_bad, $tabel1_good, $content);

// Fix Tabel 2 end
$tabel2_bad = <<<BLADE
            </x-table>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ \$paginatedTable1->links('pagination::tailwind') }}
            </div>
        </div>
    </x-card>
BLADE;

$tabel2_good = <<<BLADE
            </x-table>
            </div>
        </form>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ \$paginatedTable2->links('pagination::tailwind') }}
        </div>
    </x-card>
BLADE;

$content = str_replace($tabel2_bad, $tabel2_good, $content);

file_put_contents($file, $content);
echo "Layout fixed.";
