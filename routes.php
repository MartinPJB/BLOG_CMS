<?php

use \Core\Routing\Router;

use \Controller\ArticlesController;
use \Controller\UsersController;
use \Controller\PublicController;
use \Controller\AdminController;
use \Controller\AdminSubControllers\AdminArticlesController;

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


/* -- Admin routes -- */
Router::addRoute('admin', '', AdminController::class, 2, 'GET');

/* -- Admin Articles -- */
// GET
Router::addRoute('admin', 'articles', AdminArticlesController::class, 2, 'GET');

// POST
Router::addRoute('admin', 'create_article', AdminArticlesController::class, 2, 'POST');
Router::addRoute('admin', 'edit_article', AdminArticlesController::class, 2, 'POST');
Router::addRoute('admin', 'delete_article', AdminArticlesController::class, 2, 'POST');