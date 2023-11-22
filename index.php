<?php

use \Core\Routing\RequestContext;
use \Core\Routing\Router;

// Requires autoloads
require_once 'vendor/autoload.php';
require_once 'autoload.php';

// Add routes
require_once 'routes.php';

$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$parameters = [
  'POST' => $_POST,
  'GET' => $_GET,
];

$requestContext = new RequestContext($url, $method, $parameters);
Router::dispatch($requestContext);