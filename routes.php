<?php

use \Core\Routing\Router;
use \Controller\ArticlesController;
use \Controller\CategoriesController;
use \Controller\UsersController;
use \Controller\PublicController;
use \Controller\AdminController;
use \Controller\StaticsController;
use \Controller\AdminSubControllers\AdminArticlesController;
use \Controller\AdminSubControllers\AdminBlocksController;
use \Controller\AdminSubControllers\AdminMediasController;

/*
  Usage:
  Router::addRoute('route', 'action', Controller::class, accessLevel, method);
*/

/* -- Categories routes -- */
// GET
Router::addRoute('accueil', '', '\Controller\CategoriesController', 0, 'GET');
Router::addRoute('accueil', 'all', '\Controller\CategoriesController', 0, 'GET');
Router::addRoute('accueil', 'see', '\Controller\CategoriesController', 0, 'GET');

/* -- Articles routes -- */
// GET
Router::addRoute('articles', '', '\Controller\ArticlesController', 0, 'GET');
Router::addRoute('articles', 'all', '\Controller\ArticlesController', 0, 'GET');
Router::addRoute('articles', 'see', '\Controller\ArticlesController', 0, 'GET');

/* -- Users routes -- */
// GET
Router::addRoute('users', 'see', '\Controller\UsersController', 0, 'GET');
Router::addRoute('users', 'login', '\Controller\UsersController', 0, 'GET');
Router::addRoute('users', 'logout', '\Controller\UsersController', 1, 'GET');

// POST
Router::addRoute('users', 'process_login', '\Controller\UsersController', 0, 'POST');

/* -- Public routes -- */
// GET
Router::addRoute('public', 'front', '\Controller\PublicController', 0, 'GET');
Router::addRoute('public', 'back', '\Controller\PublicController', 0, 'GET');

/* -- Admin routes -- */
Router::addRoute('admin', '', '\Controller\AdminController', 2, 'GET');

/* -- Admin Articles -- */
// GET
Router::addRoute('admin', 'articles', '\Controller\AdminSubControllers\AdminArticlesController', 2, 'GET');

// POST
Router::addRoute('admin', 'create_article', '\Controller\AdminSubControllers\AdminArticlesController', 2, 'POST');
Router::addRoute('admin', 'edit_article', '\Controller\AdminSubControllers\AdminArticlesController', 2, 'POST');
Router::addRoute('admin', 'delete_article', '\Controller\AdminSubControllers\AdminArticlesController', 2, 'POST');

/* -- Admin Blocks -- */
// GET
Router::addRoute('admin', 'blocks', '\Controller\AdminSubControllers\AdminBlocksController', 2, 'GET');

// POST
Router::addRoute('admin', 'create_block', '\Controller\AdminSubControllers\AdminBlocksController', 2, 'POST');
Router::addRoute('admin', 'edit_block', '\Controller\AdminSubControllers\AdminBlocksController', 2, 'POST');
Router::addRoute('admin', 'delete_block', '\Controller\AdminSubControllers\AdminBlocksController', 2, 'POST');
Router::addRoute('admin', 'change_block_order', '\Controller\AdminSubControllers\AdminBlocksController', 2, 'POST');

/* -- Admin Categories -- */
// GET
Router::addRoute('admin', 'categories', '\Controller\AdminSubControllers\AdminCategoriesController', 2, 'GET');

// POST
Router::addRoute('admin', 'create_category', '\Controller\AdminSubControllers\AdminCategoriesController', 2, 'POST');
Router::addRoute('admin', 'edit_category', '\Controller\AdminSubControllers\AdminCategoriesController', 2, 'POST');
Router::addRoute('admin', 'delete_category', '\Controller\AdminSubControllers\AdminCategoriesController', 2, 'POST');

/* -- Admin Medias -- */
// GET
Router::addRoute('admin', 'medias', '\Controller\AdminSubControllers\AdminMediasController', 2, 'GET');
Router::addRoute('admin', 'get_all_medias', '\Controller\AdminSubControllers\AdminMediasController', 2, 'GET');

// POST
Router::addRoute('admin', 'upload_media', '\Controller\AdminSubControllers\AdminMediasController', 2, 'POST');
Router::addRoute('admin', 'edit_media', '\Controller\AdminSubControllers\AdminMediasController', 2, 'POST');
Router::addRoute('admin', 'delete_media', '\Controller\AdminSubControllers\AdminMediasController', 2, 'POST');
Router::addRoute('admin', 'unassign_media', '\Controller\AdminSubControllers\AdminMediasController', 2, 'POST');


/* -- Statics routes -- */
// GET
Router::addRoute('credits', '', '\Controller\StaticsController', 0, 'GET');
