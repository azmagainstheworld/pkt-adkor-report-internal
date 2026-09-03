<?php

$controllerFile = 'app/Http/Controllers/PemeliharaanController.php';
$content = file_get_contents($controllerFile);

$content = str_replace(
    "['file_excel' => 'required|mimes:xlsx,xls,csv|max:51200']",
    "['file' => 'required|mimes:xlsx,xls,csv|max:51200']",
    $content
);

$content = str_replace(
    "\$request->file('file_excel')",
    "\$request->file('file')",
    $content
);

file_put_contents($controllerFile, $content);
echo "Controller updated to accept 'file' instead of 'file_excel'.\n";
