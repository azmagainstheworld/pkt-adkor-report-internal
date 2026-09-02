<?php
function removeConstructor() {
    $path = 'app/Http/Controllers/Admin/UserController.php';
    $content = file_get_contents($path);
    
    $constructor = <<<PHP
    public function __construct()
    {
        \$this->middleware(function (\$request, \$next) {
            abort_if(!auth()->user()->isAdmin(), 403, 'Akses ditolak. Hanya Admin yang dapat mengakses Manajemen Pengguna.');
            return \$next(\$request);
        });
    }

PHP;

    $content = str_replace($constructor, "", $content);
    file_put_contents($path, $content);
    echo "UserController constructor removed.\n";
}

removeConstructor();
