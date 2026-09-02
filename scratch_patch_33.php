<?php
$path = 'scratch_bulk_perizinan.php';
$content = file_get_contents($path);

// Ensure the JS block ends with </script>
$content = str_replace("toggleDeleteBtnProses();\n        }\nEOD;", "toggleDeleteBtnProses();\n        }\n    </script>\nEOD;", $content);

// Replace the destructive str_replace('<script>') with a safe insertion at the end of the file
$badReplace = '$content = str_replace(\'<script>\', $js . "\n\n<script>", $content);';
$goodReplace = '$endsectionPos = strrpos($content, \'@endsection\');
if ($endsectionPos !== false) {
    $content = substr_replace($content, "\n" . $js . "\n", $endsectionPos, 0);
}';
$content = str_replace($badReplace, $goodReplace, $content);

file_put_contents($path, $content);
echo "Fixed scratch_bulk_perizinan.php\n";
?>
