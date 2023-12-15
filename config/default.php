<?php

use \Core\Config;

function convertToBytes($value)
{
  $unit = strtoupper(substr($value, -1));
  $size = (int)$value;

  switch ($unit) {
    case 'P':
      $size *= 1024;
    case 'T':
      $size *= 1024;
    case 'G':
      $size *= 1024;
    case 'M':
      $size *= 1024;
    case 'K':
      $size *= 1024;
  }

  return $size;
}

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
    'file_size_limit' => convertToBytes(ini_get('upload_max_filesize'))
  ],
];

// Set as configuration
Config::setConfig($config);

// Delete the configuration variable
unset($config);