# Compatibility

## Declared Composer constraints

The manifest declares these runtime requirements:

| Dependency | Constraint |
| --- | --- |
| PHP | `^7.3 || ^8.0` |
| `illuminate/support` | `~7|~8|~9|~10|~11|~12|~13` |

Development-only requirements are PHPUnit `^11.0 || ^12.0` and Orchestra Testbench `^10.0 || ^11.0`.

The package metadata sets `minimum-stability` to `dev`; this is a Composer resolution policy, not a claim that every development revision is production-ready.

## Laravel integration

The package relies on APIs from `illuminate/support`, including the service container, package service provider, cache facade, filesystem facade, and string helpers. Laravel package discovery is configured through the `extra.laravel.providers` entry.

The repository's CI exercises PHP 8.2 and 8.3, including a dedicated Laravel 13 job. Those jobs are direct evidence for those combinations only; the broader Composer constraints are declared compatibility ranges and should be verified in the consuming application's dependency graph.

## Runtime caveats

- The source contains typed return declarations such as `cacheKey(): string`, `forget(): void`, and `times(): array`; consumers must use a PHP version that can parse the package's current source.
- `forget()`'s wildcard branch calls `Cache::forgetByWildcard()`. Ensure the application's cache layer supplies that method before using wildcard invalidation.
- Discovery uses `File::files()` and a lightweight namespace/class parser. Complex declarations, anonymous classes, or classes unavailable to Composer autoloading are not promised to resolve.
- The package has no bundled configuration file. `times()` reads `cache.retriever_times` when the host application defines it, otherwise environment-backed defaults are used.
