<?php
$f2 = 'app/Http/Controllers/PaNonTekstualController.php';
$c2 = file_get_contents($f2);

if (strpos($c2, "use Illuminate\Pagination\LengthAwarePaginator;") === false) {
    $c2 = str_replace(
        "use Illuminate\Support\Facades\DB;",
        "use Illuminate\Support\Facades\DB;\nuse Illuminate\Pagination\LengthAwarePaginator;",
        $c2
    );
    file_put_contents($f2, $c2);
    echo "Added LengthAwarePaginator import.\n";
} else {
    echo "Already imported.\n";
}
