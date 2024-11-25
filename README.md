# Herbalstore

Website template for herbal stores, built with Drupal 9.

## Required software

- [DDEV](https://ddev.com)
- [envsubst](https://www.gnu.org/software/gettext/manual/html_node/envsubst-Invocation.html)

## Installation

### DDEV

#### Install

To install a copy of the latest production environment:

```
$ git checkout master
$ ./scripts/install.sh
```

#### Update

To execute all database updates needed to upgrade the production environment to newly developed code:

```
$ ./scripts/update.sh
```

#### PHPStorm server setup

* name: `herbalstore.ddev.site`
* host: `herbalstore.ddev.site`
* port: 80
* debugger: XDebug
* use path mappings: checked
* map the root project to the absolute path `/app` on the server
