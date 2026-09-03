<?php

$file = 'resources/views/kearsipan-pa-teknik.blade.php';
$content = file_get_contents($file);

// Find the first </x-table> and replace it with </x-table></form>
// but we need to do it precisely for Tabel 1 and Tabel 2
$parts = explode('</x-table>', $content);
if (count($parts) >= 3) {
    // There are at least two tables
    // Check if the form is already closed right after
    if (strpos($parts[1], '</form>') !== 0 && strpos(trim($parts[1]), '</form>') !== 0) {
        $parts[0] .= "</x-table>\n        </form>";
    } else {
        $parts[0] .= "</x-table>";
    }

    if (strpos($parts[2], '</form>') !== 0 && strpos(trim($parts[2]), '</form>') !== 0) {
        $parts[1] .= "</x-table>\n        </form>";
    } else {
        $parts[1] .= "</x-table>";
    }
    
    $content = implode('', $parts);
    file_put_contents($file, $content);
    echo "Added closing form tags for bulk delete forms.\n";
} else {
    echo "Could not find </x-table> tags.\n";
}
