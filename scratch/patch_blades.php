<?php
// Patch PA Tekstual
$file1 = 'resources/views/kearsipan-pa-non-teknik-tekstual.blade.php';
$content1 = file_get_contents($file1);

// Replace dataTable1 loop
$content1 = str_replace(
    "@forelse(\$dataTable1 as \$row)",
    "@forelse(\$paginatedTable1 as \$row)",
    $content1
);

// Add links for Table 1
$content1 = str_replace(
    "</x-table>\n        </div>\n    </x-card>",
    "</x-table>\n            <div class=\"px-6 py-4 border-t border-gray-100 bg-gray-50\">\n                {{ \$paginatedTable1->links('pagination::tailwind') }}\n            </div>\n        </div>\n    </x-card>",
    $content1
);

// Replace dataTable2 loop
$content1 = str_replace(
    "@forelse(\$dataTable2 as \$row)",
    "@forelse(\$paginatedTable2 as \$row)",
    $content1
);

// Add links for Table 2
$content1 = str_replace(
    "</x-table>\n        </div>\n    </x-card>\n\n    <!-- MODAL TAMBAH JENIS DOKUMEN -->",
    "</x-table>\n            <div class=\"px-6 py-4 border-t border-gray-100 bg-gray-50\">\n                {{ \$paginatedTable2->links('pagination::tailwind') }}\n            </div>\n        </div>\n    </x-card>\n\n    <!-- MODAL TAMBAH JENIS DOKUMEN -->",
    $content1
);

file_put_contents($file1, $content1);
echo "Patched kearsipan-pa-non-teknik-tekstual.blade.php\n";

// Patch PA Non Tekstual
$file2 = 'resources/views/non-teknik-non-tekstual.blade.php';
$content2 = file_get_contents($file2);

// Replace dataPa loop
$content2 = str_replace(
    "@forelse(\$dataPa as \$row)",
    "@forelse(\$paginatedDataPa as \$row)",
    $content2
);

// Add links for Table
$content2 = str_replace(
    "</x-table>\n        </div>\n    </x-card>\n\n    <!-- MODAL TAMBAH DATA (Biasa) -->",
    "</x-table>\n            <div class=\"px-6 py-4 border-t border-gray-100 bg-gray-50\">\n                {{ \$paginatedDataPa->links('pagination::tailwind') }}\n            </div>\n        </div>\n    </x-card>\n\n    <!-- MODAL TAMBAH DATA (Biasa) -->",
    $content2
);

file_put_contents($file2, $content2);
echo "Patched non-teknik-non-tekstual.blade.php\n";
