<?php

use \Core\Routing\Router;

use \Controller\ArticlesController;
use \Controller\UsersController;
use \Controller\PublicController;

/*
  Usage:
  Router::addRoute('route', 'action', Controller::class, accessLevel, method);
*/

/* -- Articles routes -- */
// GET
Router::addRoute('articles', '', ArticlesController::class, 0, 'GET');
Router::addRoute('articles', 'list', ArticlesController::class, 0, 'GET');
Router::addRoute('articles', 'see', ArticlesController::class, 0, 'GET');


/* -- Users routes -- */
// GET
Router::addRoute('users', 'see', UsersController::class, 0, 'GET');
Router::addRoute('users', 'login', UsersController::class, 0, 'GET');
Router::addRoute('users', 'logout', UsersController::class, 1, 'GET');

// POST
Router::addRoute('users', 'process_login', UsersController::class, 0, 'POST');


/* -- Public routes -- */
// GET
Router::addRoute('public', 'front', PublicController::class, 0, 'GET');
Router::addRoute('public', 'back', PublicController::class, 0, 'GET');