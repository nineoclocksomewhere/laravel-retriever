# Testing and verification

## Local checks

From the repository root, run:

```sh
composer validate --strict
composer test
```

PHP syntax can be checked without changing the package:

```sh
for file in $(git ls-files '*.php'); do php -l "$file" || exit 1; done
```

Before committing documentation changes:

```sh
git diff --check
```

The PHPUnit configuration loads `vendor/autoload.php` and runs both `tests/Unit` and `tests/Feature`. The test case boots Orchestra Testbench, registers the package provider, and adds `tests/Cache` under the `retriever` namespace.

## What the tests cover

- `RetrieveTest` checks `get()`-method defaults and explicit `red`, `green`, and `blue` method resolution.
- `RetrieverTest` checks a namespaced default method, camel-cased method lookup (`animals_of_the_world.mammals`), and an invalid manager method throwing `BadMethodCallException`.

The tests do not constitute exhaustive coverage of custom namespaces, TTL configuration, cache-key hashing, wildcard invalidation, missing retrievers, or every supported Laravel minor version.

## CI

`.github/workflows/php.yml` runs on pushes and pull requests targeting `master`:

- `legacy`: PHP 8.2 and 8.3 matrix, Composer validation, dependency update, and `composer run-script test`.
- `laravel13`: PHP 8.3, the same validation/install/test sequence for the Laravel 13 line.

CI supplies `COMPOSER_AUTH` from the repository secret during dependency installation. A missing or invalid secret can block dependency resolution before tests run; that is an infrastructure result, not evidence that the package tests failed.
