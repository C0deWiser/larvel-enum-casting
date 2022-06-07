# Laravel Enum Casting

[![PHP Composer](https://github.com/C0deWiser/larvel-enum-casting/actions/workflows/php.yml/badge.svg)](https://github.com/C0deWiser/larvel-enum-casting/actions/workflows/php.yml)

This package brings enums casting into Laravel projects.

## Install

    composer require codewiser/laravel-enum-casting

## Database

We suppose that enums stored in database either as `set` or as `json` object.

### Set

    superuser,adminstrator

To cast enums from `set` datatype, use `set` keyword.

### JSON

    ["superuser","adminstrator"]

To cast enums from `json` datatype, use `json` or `array` keyword.

## Usage

> See also [Laravel documentation](https://laravel.com/docs/9.x/eloquent-mutators#array-and-json-casting)

### AsArray

Use `AsArray` to cast enums as a simple `array`. Provide datatype and Enum class as arguments:

```php
use \Codewiser\Enum\Castable\AsArray;

/**
 * The attributes that should be cast.
 *
 * @var array
 */
protected $casts = [
    'roles' => AsArray::class . ':set,' . MyEnum::class,
];
```

### AsArrayObject

Use `AsArrayObject` to cast enums as `ArrayObject`. Provide datatype and Enum class as arguments:

```php
use \Codewiser\Enum\Castable\AsArrayObject;

/**
 * The attributes that should be cast.
 *
 * @var array
 */
protected $casts = [
    'roles' => AsArrayObject::class . ':json,' . MyEnum::class,
];
```

### AsCollection

Use `AsCollection` to cast enums as `Collection`. Provide datatype and Enum class as arguments. Optionally you may pass custom collection class name:

```php
use \Codewiser\Enum\Castable\AsCollection;

/**
 * The attributes that should be cast.
 *
 * @var array
 */
protected $casts = [
    'roles' => AsCollection::class . ':array,' . MyEnum::class . ',' . MyCollection::class,
];
```

> Arguments order has no matter.
