<?php

declare(strict_types = 1);

use Robo\Tasks;

/**
 * Robo task runner configuration for the Herbal Store project.
 */
class RoboFile extends Tasks {

  /**
   * Generates the behat.yml configuration file.
   *
   * The `behat.yml.dist` file will be copied to `behat.yml` and the
   * environment variables in it will be replaced.
   */
  public function behatGenerateConfig(): void {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ['.env', '.env.dist']);
    $dotenv->load();

    $replace = ['${WEBROOT}' => __DIR__ . '/web'];

    $environment_variables = [
      'BASE_URL',
      'SCREENSHOTS_PATH',
      'WEBDRIVER_HOST',
    ];
    foreach ($environment_variables as $environment_variable) {
      $value = $_ENV[$environment_variable];
      if ($value === FALSE) {
        throw new \InvalidArgumentException(
          "Environment variable $environment_variable is not set."
        );
      }
      $replace['${' . $environment_variable . '}'] = $value;
    }

    $this->taskFilesystemStack()
      ->copy('behat.yml.dist', 'behat.yml', TRUE)
      ->run();
    $this->taskReplaceInFile('behat.yml')
      ->from(array_keys($replace))
      ->to(array_values($replace))
      ->run();
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

}
