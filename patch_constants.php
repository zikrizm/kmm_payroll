<?php

$file = __DIR__ . '/config/constants.php';
$content = file_get_contents($file);
$pos = strpos($content, "'langs'");
if ($pos === false) {
    fwrite(STDERR, "langs not found\n");
    exit(1);
}

$header = <<<'PHP'
<?php

$zktecoPort = env('ZKTECO_PORT', '8000');

// Hostname from payroll URL (e.g. http://100.88.185.78:8080/login → 100.88.185.78).
$zktecoRawHost = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
$zktecoHost = parse_url('http://' . $zktecoRawHost, PHP_URL_HOST) ?: '127.0.0.1';
$zktecoIp = gethostbyname($zktecoHost);

if (in_array(strtolower($zktecoHost), ['localhost', '127.0.0.1'], true)
    && !empty($_SERVER['SERVER_ADDR'])
    && !in_array($_SERVER['SERVER_ADDR'], ['127.0.0.1', '::1'], true)
) {
    $zktecoIp = $_SERVER['SERVER_ADDR'];
}

// Optional override via ZKTECO_HOST in .env (leave unset to follow request hostname).
if ($overrideHost = env('ZKTECO_HOST')) {
    $zktecoIp = $overrideHost;
}

$zktecoBaseUrl = 'http://' . $zktecoIp . ':' . $zktecoPort;

return [

    /*
    |--------------------------------------------------------------------------
    | App Constants
    |--------------------------------------------------------------------------
    |List of all constants for the app
    */
    'api_zkteco_internal' => $zktecoBaseUrl,
    'api_zkteco' => $zktecoBaseUrl,
    'api' => $zktecoBaseUrl,

PHP;

file_put_contents($file, $header . substr($content, $pos));
echo "patched\n";
