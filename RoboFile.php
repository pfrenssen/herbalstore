<?php

declare(strict_types = 1);

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Robo\Tasks;

/**
 * Robo task runner configuration for the Herbal Store project.
 */
class RoboFile extends Tasks implements LoggerAwareInterface {

  use LoggerAwareTrait;

  /**
   * Sets up the development environment.
   */
  public function devSetup() {
    $this->behatGenerateConfig();
    $this->drushGenerateConfig();
  }

  /**
   * Generates the behat.yml configuration file.
   *
   * The `behat.yml.dist` file will be copied to `behat.yml` and the environment
   * variables in it will be replaced.
   */
  public function behatGenerateConfig(): void {
    $this->generateConfig('behat.yml.dist', 'behat.yml');
  }

  /**
   * Generates the drush.yml configuration file.
   *
   * The `drush.yml.dist` file will be copied to `drush.yml` and the environment'
   * variables in it will be replaced.
   */
  public function drushGenerateConfig(): void {
    $this->generateConfig('drush/drush.yml.dist', 'drush/drush.yml');
  }

  /**
   * Enables development mode.
   */
  public function siteDevMode(): void {
    $this->output()->writeln('$ cp ./web/sites/example.settings.local.php ./web/sites/default/settings.local.php');
    $this->output()->writeln('$ cp ./web/sites/default/default.services.yml ./web/sites/default/services.yml');
    $this->output()->writeln('Uncomment the section about settings.local.php in ./web/sites/default/settings.php');
    $this->output()->writeln('Set `debug = true` in ./web/sites/default/services.yml');
  }

  /**
   * Enables or disables HTTP Basic Authentication.
   *
   * This command will enable HTTP Basic Authentication if the credentials are
   * set in the environment variables BASIC_AUTH_USERNAME and BASIC_AUTH_PASSWORD.
   *
   * If either of these variables are not set basic authentication will be disabled.
   *
   * Make sure to run this every time after scaffolding Drupal core.
   */
  public function environmentSetupBasicAuth(): void {
    // Remove the existing .htpasswd file.
    $this->taskFilesystemStack()->remove('web/.htpasswd')->run();

    // Remove the basic auth directive from the .htaccess file if it exists.
    $this->taskReplaceInFile('web/.htaccess')
      ->regex('/^AuthType Basic\n.*\n.*\nRequire valid-user\n/m')
      ->to('')
      ->run();

    // Check that the basic authentication credentials are defined.
    foreach (['BASIC_AUTH_USERNAME', 'BASIC_AUTH_PASSWORD'] as $env_var) {
      if (empty($_SERVER[$env_var])) {
        $this->logger->notice('Basic authentication has been disabled since the credentials are not set.');
        return;
      }
    }

    // Generate a fresh .htpasswd file.
    $result = $this->taskExec('htpasswd')
      ->option('-b') // Provide the password as an argument.
      ->option('-c') // Create a new file.
      ->arg('web/.htpasswd')
      ->arg($_SERVER['BASIC_AUTH_USERNAME'])
      ->arg($_SERVER['BASIC_AUTH_PASSWORD'])
      ->run();

    if (!$result->wasSuccessful()) {
      $this->logger->error('.htpasswd file could not be written');
    }

    // Add the directive to enable basic authentication.
    $htpasswd_path = getcwd() . '/web/.htpasswd';
    $this->taskWriteToFile('web/.htaccess')
      ->append()
      ->lines([
        'AuthType Basic',
        'AuthName "Restricted access"',
        'AuthUserFile ' . $htpasswd_path,
        'Require valid-user',
      ])
      ->run();
  }

  /**
   * Generates a configuration file.
   *
   * This will copy the source file to the destination file and replace any
   * environment variables in it, as long as they are declared as `${ENV_VAR}`.
   * If the destination file exists it will be overwritten.
   *
   * @param string $source
   *   The path to the source file, relative to the project root.
   * @param string $destination
   *   The path to the destination file, relative to the project root.
   */
  protected function generateConfig(string $source, string $destination): void {
    $replace = [];

    foreach (array_keys($_SERVER) as $env_var) {
      $value = $_SERVER[$env_var];
      if (is_scalar($value)) {
        $replace['${' . $env_var . '}'] = $value;
      }
    }

    $this->taskFilesystemStack()
      ->copy($source, $destination, TRUE)
      ->run();

    $this->taskReplaceInFile($destination)
      ->from(array_keys($replace))
      ->to(array_values($replace))
      ->run();
  }

}
