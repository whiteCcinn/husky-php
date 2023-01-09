# PHP Husky 🐺
[![Latest Stable Version](http://poser.pugx.org/ccinn/husky-php/v)](https://packagist.org/packages/ccinn/husky-php)
[![Total Downloads](http://poser.pugx.org/ccinn/husky-php/downloads)](https://packagist.org/packages/ccinn/husky-php)
[![Latest Unstable Version](http://poser.pugx.org/ccinn/husky-php/v/unstable)](https://packagist.org/packages/ccinn/husky-php)
[![License](http://poser.pugx.org/ccinn/husky-php/license)](https://packagist.org/packages/ccinn/husky-php)
[![PHP Version Require](http://poser.pugx.org/ccinn/husky-php/require/php)](https://packagist.org/packages/ccinn/husky-php)

PHP is combined with composer to implement functionality similar to js's NPM module husky

> Git hooks made easy

Husky can prevent bad `git commit`, `git push` and more git's hooks

## Code Contributors

This project exists thanks to all the people who contribute. [[Contribute](https://github.com/whiteCcinn/husky-php/graphs/contributors)].
<a href="https://github.com/whiteCcinn/husky-php/graphs/contributors"><img src="https://opencollective.com/husky-php/contributors.svg?width=890&button=false" /></a>

## Branch

- v5.6-7.3.x => "ccinn/husky-php": "^0.1.0"
- v7.4 => "ccinn/husky-php": "^0.2.0"
- v8.0.0 => "ccinn/husky-php": "^0.4.0"


## Install

In composer.json

```
    "require-dev": {
        "ccinn/composer-husky-plugin": "^0.1.0",
        "ccinn/husky-php": "^0.4.0"
    },
```

Or

In Shell

```sh
composer require --dev ccinn/composer-husky-plugin ccinn/husky-php
```

## Docker

```
docker build --build-arg PHP_VERION=8.0.9 -t husky-php .
```

## Usage

you can also configure hooks using `.huskyrc` or `.huskyrc.json` file.

```json5
// .huskyrc or .huskyrc.json
{
  "hooks": {
    "pre-commit": "echo 'husky-php-pre-commit'",
    "pre-push": "echo 'husky-php-pre-push'",
    "...": "..."
  }
}
```

Running the git action hooks will be triggered

```sh
git commit -m 'Keep calm and commit'
```

You will see

```
husky-php-pre-commit
```

## Default

By default, the pre - commit

Default support features:

1. Detect code conflicts

2. Test code specifications

3. Check code syntax

## Window User

You need run in `bash` environment, for example：`GitBash`
