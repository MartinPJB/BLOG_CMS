<?php

// Autoload classes
spl_autoload_register(function ($className) {
  $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

  $root = __DIR__;
  $file = $root . DIRECTORY_SEPARATOR . $className . '.php';

  if (file_exists($file)) {
      require $file;
  }
});