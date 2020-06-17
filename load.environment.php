<?php

/**
 * This file is included very early. See autoload.files in composer.json and
 * https://getcomposer.org/doc/04-schema.md#files
 */

use Dotenv\Dotenv;

// Load environment variables.
foreach (['.env', '.env.dist'] as $file) {
  $path = __DIR__ . DIRECTORY_SEPARATOR . $file;
  if (is_file($path) && is_readable($path)) {
    $dotenv = Dotenv::createImmutable(__DIR__, $file);
    $dotenv->load();
  }
}

// Set dynamic environment variables.
$dynamic_vars = [
  'PROJECTROOT' => __DIR__,
  'WEBROOT' => __DIR__ . DIRECTORY_SEPARATOR . 'web',
];
foreach ($dynamic_vars as $name => $value) {
  $_ENV[$name] = $value;
  $_SERVER[$name] = $value;
}
