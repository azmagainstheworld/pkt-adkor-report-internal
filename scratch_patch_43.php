<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// The problem is that the dropdown is in a container with z-20.
// Laravel's pagination links (Tailwind) often use z-indexes up to z-30 for active/focus states.
// Because the pagination is later in the DOM and has a higher or equal z-index, it clips the dropdown!
// We can fix this by elevating the header container to a much higher z-index like z-[60].

$search = 'class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 relative z-20"';
$replace = 'class="p-5 border-b border-gray-100 bg-white flex justify-between items-center flex-wrap gap-4 relative z-[60]"';

$content = str_replace($search, $replace, $content);

file_put_contents($path, $content);
echo "Z-index increased to z-[60] to prevent pagination clipping!\n";
?>
