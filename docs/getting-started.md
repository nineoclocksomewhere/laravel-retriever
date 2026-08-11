# Getting started

## Requirements

The package manifest declares:

- PHP `^7.3 || ^8.0`.
- `illuminate/support` `~7|~8|~9|~10|~11|~12|~13`.

Install it into an existing Laravel application with:

```sh
composer require nocs/laravel-retriever
```

The package uses Laravel package discovery, so no manual provider registration is normally needed.

## Create a retriever

Put a class in either `app/Retrievers` or `app/Cache` and use the corresponding `App\Retrievers` or `App\Cache` namespace. Public methods are invoked as the value-producing methods.

```php
<?php

namespace App\Retrievers;

class Countries
{
    public function europe(): array
    {
        return ['nl', 'be', 'de'];
    }
}
```

Call it with a dotted key:

```php
$countries = retriever()->get('countries.europe');
```

A class can expose `get()` as its default method:

```php
<?php

namespace App\Cache;

class MaintenanceMode
{
    public function get(): bool
    {
        return false;
    }
}
```

```php
$isMaintenance = retriever('maintenance_mode');
```

## Verify the integration

Clear application caches if necessary, then call a retriever from a request, job, command, or test. The first lookup invokes the method and stores its result; subsequent lookups use the generated cache key until it expires or is forgotten.

For package development, run:

```sh
composer test
```
