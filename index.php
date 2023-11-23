<?php

use \Core\RequestContext;
use \Core\Routing\Router;
use \Core\Database\Manager;

// Requires autoloads
require_once 'vendor/autoload.php';
require_once 'autoload.php';

// Config import
require_once 'config/default.php';

// Database connection
Manager::getConnection($config['database']);
Manager::connectToDatabase($config['database']['name']);


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
