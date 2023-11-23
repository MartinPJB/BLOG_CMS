<?php

use \Core\Config;

// Default configuration
$config = [
  'database' => [
    'host' => 'localhost',
    'name' => 'cuej_blog',
    'user' => 'root',
    'password' => '',
  ],

  'admin' => [
    'name' => 'admin',
    'email' => 'admin@site.example',
    'password' => password_hash('admin', PASSWORD_DEFAULT),
  ],

  'site' => [
    'name' => 'CUEJ_CMS Blog',
    'description' => 'Un blog pour le CUEJ_CMS',
    'root' => 'http://' . $_SERVER['HTTP_HOST'] . '/CUEJ_CMS/',
    'default_route' => 'articles',
    'language' => 'fr',
  ],
];

// Set as configuration
Config::setConfig($config);

// Delete the configuration variable
unset($config);