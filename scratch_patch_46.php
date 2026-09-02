<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// The modal is z-50.
// If we set the header to z-60, it overlaps the modal.
// If we set it to z-20, it gets clipped by the pagination (which seems to have z-20 or z-30 in Tailwind).
// The sweet spot is z-40. It will overlap the pagination, but stay under the modal.

$content = str_replace('relative z-20', 'relative z-40', $content);

file_put_contents($path, $content);
echo "Z-index set to z-40!\n";
?>
