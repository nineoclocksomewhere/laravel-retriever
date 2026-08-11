# Architecture

## Purpose

Laravel Retriever is a small convention-based adapter between application classes and Laravel's cache repository. It does not expose an HTTP API or persist retriever definitions in a database.

## Resolution lifecycle

1. Laravel package discovery loads `RetrieverServiceProvider`.
2. `register()` binds one `RetrieverManager` instance as `retriever`.
3. `boot()` registers `app/Retrievers` and `app/Cache`.
4. A key is parsed by `splitName()` into namespace, class, and method.
5. `caller()` scans the paths for PHP files, extracts simple namespace/class declarations, instantiates autoloadable classes, and caches instances in `$callers`.
6. `callable()` confirms the normalized method exists.
7. `get()` builds a deterministic cache key and uses Laravel `Cache::remember()` to invoke the method only when the cache misses.

The manager keeps registered paths in `$paths` and resolved class instances (including misses) in `$callers` for the lifetime of the manager singleton.

## Conventions and boundaries

- A retriever is an ordinary, instantiable PHP class; methods are called on an instance and may accept the parameter values supplied to `get()`.
- Discovery is file-based and only recognizes the simple namespace/class declaration pattern implemented by `getClassFromFile()`.
- Classes must be autoloadable when discovered. The package does not dynamically include arbitrary files.
- The cache key includes the caller key and serialized parameters. It does not include the TTL.
- A failed resolution is cached as a null caller for the manager lifetime, so register paths before the first lookup for a key.
- Application paths are registered under the same `app` namespace by the service provider. If an application uses both directories, class-name collisions should be avoided.
- The wildcard invalidation method is an integration dependency: the source calls `Cache::forgetByWildcard()` when a wildcard key is requested.

## Extension point

A package or application service provider can call `loadRetrieversFrom($path, $namespace)` to add another namespace. The manager and facade are the supported entry points; no routes, controllers, events, jobs, or webhooks are involved.
