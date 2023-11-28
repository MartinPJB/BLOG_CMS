<?php

use \Core\RequestContext;
use \Core\Routing\Router;

use \Core\Database\Manager;
use \Core\Database\Installer;
use \Core\Config;

// Requires autoloads
require_once 'vendor/autoload.php';
require_once 'autoload.php';

// Config import
require_once 'config/default.php';

// Database connection || installation
Manager::getConnection();
try {
  Manager::connectToDatabase(Config::get('database_name'));
} catch (PDOException $e) {
  Installer::install();
}

// Session start
session_start();

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
