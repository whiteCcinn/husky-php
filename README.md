# php-husky

PHP is combined with composer to implement functionality similar to js's NPM module husky

> Git hooks made easy

Husky can prevent bad `git commit`, `git push` and more git's hooks

## Install

In composer.json

```
    "require-dev": {
        "ccinn/composer-husky-plugin": "^0.1.0",
        "ccinn/husky-php": "^0.1.0"
    },
```

Or

In Shell

```sh
composer require -dev ccinn/composer-husky-plugin ccinn/husky-php
```

## Usage

you can also configure hooks using `.huskyrc` or `.huskyrc.json` file.

```json
// .huskyrc or .huskyrc.json
{
  "husky": {
    "hooks": {
      "pre-commit": "echo 'huksy-php-pre-commit'",
      "pre-push": "echo 'huksy-php-pre-push'",
      "...": "..."
    }
  }
}
```

Running the git action hooks will be triggered

```sh
git commit -m 'Keep calm and commit'
```

You will see

```
huksy-php-pre-commit
```

## Default

By default, the pre - commit

Integrated with PHP-cs-fixer

So when you leave the pre-commit hook undefined, it will perform the action that will automatically format the code for you
