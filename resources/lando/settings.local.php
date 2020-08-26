<?php

$databases['default']['default'] = [
  'driver' => 'mysql',
  'database' => 'drupal8',
  'username' => 'drupal8',
  'password' => 'drupal8',
  'host' => 'database',
  'port' => 3306,
  'prefix' => '',
];

$settings['hash_salt'] = md5(getenv('LANDO_HOST_IP'));
$settings['trusted_host_patterns'][] = '^.*\.lndo\.site$';
$settings['config_sync_directory'] = '../config/sync';
$settings['file_temp_path'] = '../tmp';
