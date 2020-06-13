<?php

/**
 * This file is included very early. See autoload.files in composer.json and
 * https://getcomposer.org/doc/04-schema.md#files
 */

use Dotenv\Dotenv;

// Load environment variables.
$dotenv = Dotenv::createImmutable(__DIR__, ['.env.dist', '.env'], FALSE);
$dotenv->load();

// Set dynamic environment variables.
$_ENV['PROJECTROOT'] = __DIR__;
$_ENV['WEBROOT'] = __DIR__ . '/web';
