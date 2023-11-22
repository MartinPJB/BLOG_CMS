<?php

use \Router\Router;
use \Controller\UserController;

// string $routeName, string $action, mixed $controller, string $method = 'GET', array $params = []
Router::addRoute('user', 'index', new UserController());