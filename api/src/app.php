<?php

use App\Models\User;
use App\System\DatabaseConnector;

// define required REST API headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Return with ok if the REQUEST_METHOD is OPTIONS 
if ($requestMethod === 'OPTIONS') {
    die('ok');
}

// Set-up database connection
$dbConnection = (new DatabaseConnector())->getConnection();

// Insert the default user if it already doesn't exist in the database
(new User($dbConnection))->createDefaultUser();

// Get all the routes
$routes = require_once __DIR__ . '/routes.php';

/**
 * Handle application routes
 * 
 * @param string $uri
 * @param array $routes
 * 
 * @return void
 */
function handleRoutes($uri, $routes)
{
    global $requestMethod;
    global $dbConnection;

    foreach ($routes as $route) {
        $routeMethod = $route['httpMethod'] ?? $requestMethod;
        $re = '#^' . preg_replace("/\{(.*?)\}/", '(?<$1>[^/]+?)', $route['path']) . '$#';
        $matches = [];

        if ($routeMethod == $requestMethod && preg_match_all($re, $uri, $matches)) {
            $params = array_filter(
                $matches, fn ($k) => is_string($k), ARRAY_FILTER_USE_KEY
            );
            $params = array_map(
                fn($p) => $p[0], $params
            );

            $controller = new $route['controller']($dbConnection);

            if (isset($route['middleware'])) {
                $middleware = new $route['middleware']();
                $middleware->setDatabase($dbConnection);
                $middleware->handle($controller, [$controller, $route['method'], $params]);
            } else {
                call_user_func([$controller, $route['method']], ...array_values($params));
            }

            $controller->processRequest();
            return;
        }
    }

    http_response_code(404);
    json_encode([
        'status' => 404,
        'message' => 'No route found!',
    ]);
}

handleRoutes($uri, $routes);