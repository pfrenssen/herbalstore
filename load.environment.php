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
$_ENV['PROJECTROOT'] = __DIR__;
$_ENV['WEBROOT'] = __DIR__ . '/web';
