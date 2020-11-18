# Count the Words

Word counts and statistics for WordPress authors of all types.

## Setup Development Environment

Before starting your workstation will need the following:

* [Docker](https://www.docker.com/)
* [Lando](https://lando.dev/)

1. Clone the repository

`git@github.com:wpengine/count-the-words.git`

2. Start Lando

```bash
cd count-the-words
make start
```

When finished, Lando will give you the local URL of your site. You can finish the WordPress setup there. WooCommerce will be configured with enough sample data to get you started.

WordPress Credentials:

__URL:__ _https://count-the-words.lndo.site/wp-admin_

__Admin User:__ _admin_

__Admin Password:__ _password

## Build and Testing

The only current build asset is the .pot file for internationalization. Build it with the following:

```bash
make build
```

Note, assets will also build during the install phase.

The project uses the WP_Mock library for unit testing. Once setup run the following for unit tests:

```bash
make test-unit
```

We also use [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards) and [JSHint](http://jshint.com/) with [WordPress' JS Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/javascript/#installing-and-running-jshint). Linting will automagically be setup for you if you use [Visual Studio Code](https://code.visualstudio.com/). If you want to run it manually use the following:

```bash
make test-lint
```

or, to run an individual lint (php or javascript), use one of the following:

```bash
make test-lint-php
```

```bash
make test-lint-javascript
```

You can run all testing (all lints and unit tests) together with the following:

```bash
make test
```

Screw something up? You can reset your environment with the following. It will stop the environment and cleanup and the build files as well as anything downloaded.

```bash
make reset
```

## Preparing for release

To generate a .zip that can be uploaded through any normal WordPress plugin installation workflow, simply run the following:

```bash
make release
```
