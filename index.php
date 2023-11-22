<?php

use \Core\Routing\Router;

// Requires autoloads
require_once 'vendor/autoload.php';
require_once 'autoload.php';

// Add routes
require_once 'routes.php';

$URI = $_SERVER['REQUEST_URI'];

Router::dispatchRoute('user', 'index');