<?php

use \Core\Config;

// Default configuration
$config = [
  'database' => [
    'host' => '',
    'name' => '',
    'user' => '',
    'password' => '',
  ],

  'admin' => [
    'name' => '',
    'email' => '',
    'password' => password_hash('', PASSWORD_DEFAULT),
  ],

  'site' => [
    // ❗ After installation, these values will be read from the database and not from the Config class anymore!
    'default_name' => '',
    'default_description' => '',
    'default_route' => 'articles',
    'language' => '',

    // ✔️ These values will be read from the Config class
    'root' => 'http://' . $_SERVER['HTTP_HOST'],
    'file_size_limit' => 1e+7, // 10MB
  ],
];

// Set as configuration
Config::setConfig($config);

// Delete the configuration variable
unset($config);