<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../bootstrap.php';

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use Yaro\EcommerceProject\GraphQL\GraphQL;

$logger = $GLOBALS['logger'] ?? null;
if (!$logger) {
    throw new RuntimeException("Logger not initialized.");
}

try {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedPattern = '/^https:\/\/(?:[a-z0-9-]+--)?yy-ecommerce\.netlify\.app$/';

    if (preg_match($allowedPattern, $origin)) {
        header("Access-Control-Allow-Origin: $origin");
    }

    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit();
    }

    $dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
        $r->addRoute(['GET', 'POST', 'OPTIONS'], '/graphql', [GraphQL::class, 'handle']);
    });

    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
            break;

        case Dispatcher::METHOD_NOT_ALLOWED:
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            break;

        case Dispatcher::FOUND:
            [$class, $method] = $routeInfo[1];
            $vars = $routeInfo[2];

            $response = call_user_func([new $class($logger), $method], $vars);
            echo $response;
            break;

        default:
            echo json_encode(['message' => 'Welcome to the API']);
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage()
    ]);
    exit;
}
