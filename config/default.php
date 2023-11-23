<?php

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
    'password' => 'admin',
  ],

  'site' => [
    'name' => 'CUEJ_CMS Blog',
    'description' => 'Un blog pour le CUEJ_CMS',
    'root' => 'http://' . $_SERVER['HTTP_HOST'] . '/CUEJ_CMS_CMS/',
    'default_route' => 'articles',
  ],
];
