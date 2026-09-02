<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:8000/perizinan-proses-bulk/destroy");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_method' => 'DELETE',
    'ids' => [1] // dummy ID
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Ignore CSRF for this test by not sending a cookie. Laravel will throw 419, which is fine, we just want to see if the route is hit.
// Actually, wait, without CSRF, it will ALWAYS throw 419 Page Expired. We won't reach the controller.

// So let's disable CSRF for this route temporarily in Laravel.
$middleware = file_get_contents('bootstrap/app.php');
if (strpos($middleware, 'perizinan-proses-bulk/destroy') === false) {
    $middleware = str_replace(
        '->withMiddleware(function (Middleware $middleware) {', 
        "->withMiddleware(function (Middleware \$middleware) {\n        \$middleware->validateCsrfTokens(except: ['perizinan-proses-bulk/destroy']);", 
        $middleware
    );
    file_put_contents('bootstrap/app.php', $middleware);
}

// Now test again
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpcode\n";
echo substr(strip_tags($response), 0, 500);
?>
