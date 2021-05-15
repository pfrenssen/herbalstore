# Herbalstore

Website template for herbal stores.

## Required software

- [Lando](https://lando.dev)
- [envsubst](https://www.gnu.org/software/gettext/manual/html_node/envsubst-Invocation.html)

## Installation

Create the config files for the Lando development environment.

```
$ LANDO_PROJECT_NAME=myproject envsubst < resources/lando/.lando.yml > .lando.yml
$ LANDO_PROJECT_NAME=myproject envsubst < resources/lando/.env > .env
```

Start and configure the development environment.

```
$ lando start
$ lando robo dev:setup
```

Sync data.

```
$ ./scripts/update-from-production.sh
```
