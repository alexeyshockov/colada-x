# \Colada\x()

Helper function for simplify callbacks.

[![Latest Stable Version](https://poser.pugx.org/alexeyshockov/colada-x/v/stable)](https://packagist.org/packages/alexeyshockov/colada-x)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexeyshockov/colada-x/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexeyshockov/colada-x/?branch=master)
[![Build Status](https://api.travis-ci.org/alexeyshockov/colada-x.svg?branch=master)](http://travis-ci.org/alexeyshockov/colada-x)

## Installation

```bash
$ composer require alexeyshockov/colada-x
```

## Usage

With ColadaX:
```php
$activeUsers = array_filter($users, \Colada\x()->isActive());
```
```php
$role = 'ADMIN';
$administrators = array_filter($users, \Colada\x()->hasRole($role));
```

Instead of pure PHP:
```php
$activeUsers = array_filter($users, function ($user) {
    return $user->isActive();
});
```
```php
$role = 'ADMIN';
$activeUsers = array_filter($users, function ($user) use ($role) {
    return $user->hasRole($role);
});
```

## Useful together

Some code examples for your imagination.

### Laravel 5 Collections

```php
$activeUsers = $users->filter(\Colada\x()->isActive());
```

### PHP Collection ([schmittjoh/php-collection](https://github.com/schmittjoh/php-collection/))

```php
$activeUsers = $users->filter(\Colada\x()->isActive());
```

### functional-php ([lstrojny/functional-php](https://github.com/lstrojny/functional-php))

The library already has `partial_method` function, but it's less powerful.

```php
use Functional as F;

$activeUsers = F\select($users, \Colada\x()->isActive());
```

## Less useful, but still

### Doctrine 2 Collections (Symfony 2, Doctine 2 ORM, etc.)

```php
// __asClosure() is needed because all Doctrine's methods accept only \Closure instances :(
$hasActiveUsers = $users->exists(\Colada\x()->isActive()->__asClosure());
```

### Laravel 4 Collections

The same problem as described above with Doctrine.

## Alernatives

If you need only the basic functionaly, take a look at `invoke()`, `invoke_first()`, `invoke_if()`, `invoke_last()`, 
`invoker()` from a great [functional-php library](https://github.com/lstrojny/functional-php).
