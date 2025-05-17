<?php
declare(strict_types=1);

// ------------------------------------
// 1) PSR-4 Autoloader for SmartKey\
// ------------------------------------
spl_autoload_register(function (string $class): void {
    $prefix   = 'SmartKey\\';
    $base_dir = __DIR__ . '/src/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relative_class = substr($class, strlen($prefix));
    $file           = $base_dir
                    . str_replace('\\', '/', $relative_class)
                    . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// ------------------------------------
// 2) Routing Logic (endpoints in src/)
// ------------------------------------
header('Content-Type: application/json');

// Full request path, e.g. /SmartKey/Backend/api/customers
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Find “/api/”
$pos = strpos($uri, '/api/');
if ($pos === false) {
    http_response_code(404);
    echo json_encode(['error' => 'Invalid API path']);
    exit;
}

// Grab everything after “/api/”
$endpoint = substr($uri, $pos + 5);
$endpoint = explode('/', trim($endpoint, '/'))[0] ?? '';

if ($endpoint === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing endpoint']);
    exit;
}

// Now point to src/{endpoint}.php
$target = __DIR__ . "/src/{$endpoint}.php";
if (file_exists($target)) {
    include $target;
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}
