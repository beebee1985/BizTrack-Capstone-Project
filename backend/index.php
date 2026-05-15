<?php
// Load environment variables from .env file
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Simple router
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($request_uri, '/');

// Route to API endpoints
$routes = [
    'api/auth' => __DIR__ . '/api/auth.php',
    'api/products' => __DIR__ . '/api/products.php',
    'api/customers' => __DIR__ . '/api/customers.php',
    'api/sales' => __DIR__ . '/api/sales.php',
    'api/expenses' => __DIR__ . '/api/expenses.php',
    'api/dashboard' => __DIR__ . '/api/dashboard.php'
];

if (array_key_exists($path, $routes)) {
    require $routes[$path];
} else {
    header("Content-Type: application/json");
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Endpoint not found'
    ]);
}
