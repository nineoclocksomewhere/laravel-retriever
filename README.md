# Laravel Retriever

[![Latest Version on Packagist](https://badgen.net/packagist/v/nocs/laravel-retriever)](https://packagist.org/packages/nineoclocksomewhere/laravel-retriever)
[![Total Downloads](https://badgen.net/packagist/dt/nocs/laravel-retriever)](https://packagist.org/packages/nineoclocksomewhere/laravel-retriever)

Laravel Retriever resolves small PHP classes and methods by a string key, then memoizes their return values through Laravel's cache manager. It is useful for reusable, cacheable data providers such as lookup lists, external-data snapshots, and application-level reference data.

## Install

```sh
composer require nocs/laravel-retriever
```

Laravel package discovery registers `Nocs\\Retriever\\Providers\\RetrieverServiceProvider` automatically. The provider binds the `retriever` manager and scans `app/Retrievers` and `app/Cache` during application boot.

## Quick example

Create `app/Cache/Colors.php`:

```php
<?php

namespace App\Cache;

class Colors
{
    public function red(): string
    {
        return 'red';
    }
}
```

Retrieve it from application code:

```php
$value = retriever()->get('colors.red');
// or: $value = retriever('colors.red');
```

The class name and method segment are normalized with Laravel's `Str::snake`/`Str::camel` conventions. A class with only a `get()` method can be addressed as `retriever('colors')`.

## Documentation

- [Documentation hub](docs/README.md)
- [Getting started](docs/getting-started.md)
- [Usage](docs/usage.md)
- [Reference](docs/reference.md)
- [Architecture](docs/architecture.md)
- [Compatibility](docs/compatibility.md)
- [Testing and verification](docs/testing.md)

## Development

```sh
composer validate --strict
composer test
```

See [Testing and verification](docs/testing.md) for the CI matrix and source-level checks.

## Support and license

Report security issues privately to the maintainer rather than through the public issue tracker. This package is released under the [MIT license](license.md).
