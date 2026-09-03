<?php
$filePath = 'resources/views/surat-masuk-keluar.blade.php';
$content = file_get_contents($filePath);

// Remove the extraneous <script> tag
$content = preg_replace('/<\/script>\s*<script>/', '', $content); // In case they are adjacent, but here it's just <script> inside <script>
$content = preg_replace('/}\s*<script>\s*function openModal/', "}\n\n    function openModal", $content);

file_put_contents($filePath, $content);
echo "Blade syntax error patched.\n";
