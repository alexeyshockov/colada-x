# Colada X. PHP callbacks shorter [![Build Status](https://secure.travis-ci.org/alexeyshockov/colada-x.png)](http://travis-ci.org/alexeyshockov/colada-x)

## Installation

``` json
composer require alexeyshockov/colada-x
```

## Usage

With Colada X:
``` php
<?php

$activeUsers = array_filter($users, \Colada\x()->isActive());
```

Instead of pure PHP:
```php
<?php

$activeUsers = array_filter($users, function ($user) {
    $user->isActive()
});
```

## Useful together

Some code examples for your imagination.

### Laravel 5 Collections

```php
<?php

$activeUsers = $users->filter(\Colada\x()->isActive());
```

### PHP Collection ([schmittjoh/php-collection](https://github.com/schmittjoh/php-collection/))

```php
<?php

$activeUsers = $users->filter(\Colada\x()->isActive());
```

### functional-php

```php
<?php

use Functional as F;

$activeUsers = F\select($users, \Colada\x()->isActive());
```

### Stringy

```php
<?php

use Colada\X\ValueHelperCollection;

ValueHelperCollection::getInstance()->registerClassMethods('Stringy\\StaticStringy');

$formattedStrings = array_map(\Colada\x()->upperCaseFirst()->ensureRight('.'), $strings);
```

### Carbon


```php
<?php

use Colada\X\ValueHelperCollection;

ValueHelperCollection::getInstance()->register('toCarbon', array('Carbon\\Carbon', 'instance'));
ValueHelperCollection::getInstance()->register('parseToCarbon', array('Carbon\\Carbon', 'parse'));

$years = array_unique(
    array_map(\Colada\x()->getBirthday()->asCarbon()->year(), $users)
);
```

## Less useful, but still

### Doctrine 2 Collections (Symfony 2, Doctine 2 ORM, etc.)

```php
<?php

// __toClosure() is needed because all Doctrine's methods accept only \Closure instances :(
$hasActiveUsers = $users->exists(\Colada\x()->isActive()->__toClosure());
```

### Laravel 4 Collections

The same problem as described above with Doctrine.
