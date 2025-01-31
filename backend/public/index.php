<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use Yaro\EcommerceProject\GraphQL\GraphQL;
use Yaro\EcommerceProject\GraphQL\Resolvers\ProductResolver;
use Yaro\EcommerceProject\GraphQL\Resolvers\CategoryResolver;
use Yaro\EcommerceProject\GraphQL\Resolvers\AttributeResolver;
use Yaro\EcommerceProject\GraphQL\Resolvers\PriceResolver;
use Yaro\EcommerceProject\GraphQL\Resolvers\OrderResolver;

$logger = $GLOBALS['logger'] ?? null;

if (!$logger) {
    die("Logger not initialized.\n");
}

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute(['GET', 'POST', 'OPTIONS'], '/graphql', [GraphQL::class, 'handle']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

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

        $categoryResolver   = new CategoryResolver($logger);
        $attributeResolver  = new AttributeResolver($logger);
        $priceResolver      = new PriceResolver();
        $orderResolver      = new OrderResolver($logger);
        $productResolver    = new ProductResolver($categoryResolver, $attributeResolver, $priceResolver, $logger);

        if ($logger) {
            $response = call_user_func([new $class($logger), $method], $vars);
        } else {
            throw new \RuntimeException("Logger not initialized.");
        }

        echo $response;
        break;

    default:
        echo json_encode(['message' => 'Welcome to the eCommerce API!']);
        break;
}
