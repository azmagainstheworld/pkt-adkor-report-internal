<?php
$file = 'resources/views/program-strategis.blade.php';
$content = file_get_contents($file);

$search = '</x-table>';
$replace = "</x-table>\n            <div class=\"mt-4\">\n                {{ \$groupedProgram->links() }}\n            </div>";

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Pagination links added to view.\n";
