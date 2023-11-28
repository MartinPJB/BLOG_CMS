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
    // ❗ After installation, these values will be read from the database and not from the Config class anymore!
    'default_name' => 'CUEJ_CMS Blog',
    'default_description' => 'Un blog pour le CUEJ_CMS',
    'default_route' => 'articles',
    'language' => 'fr',

    // ✔️ These values will be read from the Config class
    'root' => 'http://' . $_SERVER['HTTP_HOST'] . '/CUEJ_CMS/',
  ],
];

// Set as configuration
Config::setConfig($config);

// Delete the configuration variable
unset($config);