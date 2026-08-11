# Laravel Retriever documentation

This documentation describes the behavior implemented by the current package source.

## Start here

1. [Getting started](getting-started.md) — install the package and create the first retriever.
2. [Usage](usage.md) — namespaces, arguments, TTLs, cache invalidation, and helper/facade access.
3. [Reference](reference.md) — public manager, facade, provider, and helper APIs.
4. [Architecture](architecture.md) — discovery, lookup, caching, and lifecycle details.
5. [Compatibility](compatibility.md) — Composer constraints and runtime caveats.
6. [Testing](testing.md) — local commands, CI, and coverage boundaries.

## Integration-surface inventory

| Surface | Status | Evidence |
| --- | --- | --- |
| HTTP/API routes | Absent | No routes or controllers are shipped. |
| Bruno/API collection | Absent | No Bruno collection or API contract is shipped. |
| Application events/listeners | Absent | No event or listener classes are shipped. |
| Jobs/queues/schedules | Absent | No job, queue, command, or scheduler integration is shipped. |
| Webhooks | Absent | No webhook endpoints or handlers are shipped. |
| CLI | Absent | No package console commands are registered. |
| Persistence/migrations | Absent | The package reads/writes Laravel's cache repository; it ships no models or migrations. |
| Views/assets | Absent | No Blade views or frontend assets are shipped. |
| Cache integration | Present | `RetrieverManager::get()` uses `Cache::remember()` and `forget()` removes keys. |
| Laravel service provider | Present | Package discovery registers `RetrieverServiceProvider`. |
| PHP test suite | Present | PHPUnit tests run through Composer and Orchestra Testbench. |
| CI | Present | GitHub Actions validates and tests PHP 8.2, 8.3, and a Laravel 13 job. |
