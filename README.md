# Laravel Enum Casting

## Install

```text
composer require codewiser/laravel-enum-casting
```

## Возвращающиеся значения

null если значений нет или значения не представлены в Enum'е

## Usage

```injectablephp
    protected $casts = [
        'staus' => 'boolean',
        'roles' => AsCollection::class.':set,'.Role::class.','.RoleCollection::class,
    ];
```

where **set** is a field type in database. It may be *set* or *json*. **Role::class** - is defined Enum type. Third 
argument is optional - it may be Illuminate\Collection item, that will be converted returned value.


## Tests