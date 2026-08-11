# Reference

## `retriever()` helper

The Composer autoload file `helpers/helpers.php` defines `retriever()` when it does not already exist.

- `retriever()` returns the application-bound `RetrieverManager`.
- `retriever($key, $parameters = [], $ttl = null)` forwards its arguments to `RetrieverManager::get()`.

## `RetrieverManager`

Namespace: `Nocs\Retriever\Support\RetrieverManager`.

| Method | Behavior |
| --- | --- |
| `__construct($app = null)` | Stores the application object; the service provider passes the Laravel application. |
| `loadRetrieversFrom($path, $namespace = null)` | Registers an existing directory under a snake-cased namespace and returns the manager. Duplicate paths are ignored. |
| `get($key, $parameters = [], $ttl = null)` | Resolves a retriever method, then caches its result with `Cache::remember()`. Returns `null` when no callable is found. |
| `cacheKey($key, $parameters = [])` | Builds `retriever.<key>` plus one `.md5(serialize(parameter))` segment per parameter. A `*` parameter is converted to a trailing `.*`. |
| `forget($key, $parameters = [])` | Removes the generated key with `Cache::forget()`, or uses `Cache::forgetByWildcard()` for a wildcard key. |
| `times()` | Returns configured `short`, `medium`, and `long` durations, with built-in defaults. |
| `splitName($name)` | Parses namespace, class, and method; the default method is `get`. |
| `caller($name)` | Scans registered directories, caches discovered class instances, and returns the matching instance or `null`. |
| `getClassFromFile($file)` | Reads a PHP file for a simple namespace/class declaration and returns the class only when it is autoloadable. |
| `callable($name)` | Returns the resolved caller and normalized method, or `null`. |
| `__call($method, $parameters)` | Supports `<short|medium|long>Time()` style lookups; other names throw `BadMethodCallException`. |

### Cache-key example

```php
$manager->cacheKey('colors.red', ['large']);// retriever.colors.red.<md5-of-serialized-large>
$manager->cacheKey('colors.red', ['*']);     // retriever.colors.red.*
```

The exact MD5 value is intentionally derived at runtime from PHP's `serialize()` output.

## Facade

`Nocs\Retriever\Support\Facades\Retriever` is a Laravel facade for the `retriever` binding. It exposes the manager's public methods statically:

```php
use Nocs\Retriever\Support\Facades\Retriever;

Retriever::get('colors.red');
Retriever::loadRetrieversFrom(__DIR__.'/Cache', 'vendor');
```

## Service provider

`Nocs\Retriever\Providers\RetrieverServiceProvider`:

1. Registers a singleton named `retriever` containing `RetrieverManager`.
2. During boot, registers `app_path('Retrievers')` and `app_path('Cache')` under the `app` namespace.

## Source and autoloading

Composer maps `Nocs\\Retriever\\` to `src/` and loads `helpers/helpers.php` as an autoloaded file. Test classes use the `Nocs\\Retriever\\Tests\\` development namespace.
