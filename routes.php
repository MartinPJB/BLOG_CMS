<?php

use \Core\Routing\Router;

use \Controller\ArticlesController;

/*
  Usage:
  Router::addRoute('route', 'action', Controller::class, accessLevel, method);
*/

/* -- Articles routes -- */
// GET
Router::addRoute('articles', '', ArticlesController::class);
Router::addRoute('articles', 'list', ArticlesController::class);
