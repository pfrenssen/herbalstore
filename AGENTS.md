# Agent Instructions

Website template for herbal stores, built with Drupal and run locally with DDEV.

## Code checks

Run PHPCS, PHPCBF, PHPStan, Behat and the config check through the project's
DDEV commands in `.ddev/commands/web/` (`ddev phpcs`, `ddev phpcbf`,
`ddev phpstan`, `ddev behat`, `ddev check-config`), not `vendor/bin/*`
directly.
