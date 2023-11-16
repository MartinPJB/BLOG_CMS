<?php

// Configuration de la base de données et du compte administrateur
$config = [
  'database' => [
    'host' => 'localhost',
    'database' => 'cuej_blog',
    'user' => 'root',
    'password'=> '',
  ],

  'admin' => [
    'name' => 'admin',
    'email' => 'admin@site.example',
    'password' => 'admin',
  ],

  'name' => 'CUEJ Blog',
  'description' => 'Un blog pour le CUEJ',
  'root' => 'http://localhost/CUEJ_CMS/',
  'default_route' => 'articles',
];

// Autoloader pour les classes
spl_autoload_register(function ($className) {
  $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
  $root = __DIR__;
  $file = $root . DIRECTORY_SEPARATOR . $className . '.php';
  if (file_exists($file)) {
      require $file;
  }
});