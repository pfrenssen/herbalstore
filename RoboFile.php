<?php

declare(strict_types = 1);

use Robo\Tasks;

/**
 * Robo task runner configuration for the Herbal Store project.
 */
class RoboFile extends Tasks {

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
