<?php
$content = file_get_contents('resources/views/program-strategis.blade.php');

// Regex to find required inputs/selects/textareas inside form
$pattern = '/(<(input|select|textarea)[^>]+required[^>]*>.*?<\/\2>|<input[^>]+required[^>]*>)/s';

$content = preg_replace_callback($pattern, function($matches) {
    $element = $matches[0];
    
    // Check if error-msg already exists right after (simplistic check within context)
    // We can just append it safely inside the replacing callback, but we must ensure we don't duplicate.
    return $element . "\n                    <span class=\"error-msg hidden text-red-500 text-xs mt-1\">Wajib diisi</span>";
}, $content);

// Clean up any double error-msg (if the previous replace already added one)
$content = preg_replace('/(<span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi<\/span>\s*){2,}/', '<span class="error-msg hidden text-red-500 text-xs mt-1">Wajib diisi</span>', $content);

file_put_contents('resources/views/program-strategis.blade.php', $content);
echo "Validation spans added.\n";
