# Usage

## Key format

Keys have the form `[namespace::]class[.method]`:

- `colors.red` resolves the `red()` method on the `Colors` class in the default application namespace.
- `colors` resolves `get()` on `Colors`.
- `vendor::colors.red` resolves the `red()` method on `Colors` in the registered `vendor` namespace.

The class portion is matched against a snake-cased short class name. The method portion is converted with Laravel's `Str::camel`, so `animals_of_the_world.mammals` calls `mammals()`.

## Arguments and TTL

`get()` accepts a key, an optional parameter array, and an optional TTL in seconds:

```php
$value = retriever()->get('reports.sales', [$accountId], 900);
```

The parameter array is passed to the retriever method and is also included in the cache key. A string parameter is accepted and normalized to a one-item array by `cacheKey()`.

The convenience helper forwards all arguments to `get()` when called with arguments:

```php
$value = retriever('reports.sales', [$accountId], 900);
```

A missing class or method returns `null`; it does not throw. A method that is absent on the manager itself can throw `BadMethodCallException`.

## Register another namespace

Packages can register a directory with the manager or facade. The directory must exist when it is registered:

```php
use Nocs\Retriever\Support\Facades\Retriever;

public function boot(): void
{
    Retriever::loadRetrieversFrom(__DIR__.'/../Retrievers', 'vendor');
}
```

Then use the namespace prefix:

```php
$value = retriever('vendor::colors.red');
```

The manager scans PHP files in registered directories and instantiates classes it can identify and autoload. Register paths after their classes are available to the application's autoloader.

## Cache invalidation

The manager exposes `forget()` with the same key and parameter inputs:

```php
retriever()->forget('reports.sales', [$accountId]);
```

Passing `'*'` as a parameter creates a wildcard suffix and calls `Cache::forgetByWildcard()`:

```php
retriever()->forget('reports.sales', '*');
```

The wildcard path depends on a cache implementation or macro that provides `forgetByWildcard`; Laravel's standard cache contract does not provide that method by itself.

## Configured cache times

`times()` reads `cache.retriever_times`. If that configuration value is absent, defaults are:

| Name | Default |
| --- | ---: |
| `short` | 900 seconds |
| `medium` | 3,600 seconds |
| `long` | 86,400 seconds |

The manager also recognizes dynamic calls such as `shortTime()`, `mediumTime()`, and `longTime()`. Unknown time names fall back to `medium`, or 3,600 seconds.
