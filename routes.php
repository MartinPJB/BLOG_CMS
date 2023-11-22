<?php

use \Core\Routing\Router;
use \Controller\UserController;

/*
  Usage:
  Router::addRoute('route', 'action', Controller::class, accessLevel, method);
*/

Router::addRoute('users', 'index', UserController::class);
