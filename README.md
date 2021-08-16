# laravel-retriever

Retrieve cached data made simple

## Installation

Install with composer
```sh
composer require nocs/laravel-retriever
```

## Usage

Place your cache files under app/Retrievers (primary) or app/Cache (secondary).

Use the __App\Retrievers__ or __App\Cache__ namespace for your classes.

Use, as usual, _studlied_ strings for your classnames. When retrieving use a _snaked_ version in your key.

Define public non-static methods which return the cached values.

### Basic usage

Example:
_app/Cache/Colors.php_
```php
<?php

namespace Nocs\Retriever\Tests\Cache;

class Colors {

    public function red()
    {
        return 'red';
    }

}
```
Retrieve the value with:
```php
<?php

$value = retriever()->get('colors.red');
```

### Using one method

When using only one method, you can use the _get_ method.
_app/Cache/Colors.php_
```php
<?php

namespace Nocs\Retriever\Tests\Cache;

class Colors {

    public function get()
    {
        return 'red';
    }

}
```
And retrieve the value with:
```php
<?php

$value = retriever()->get('colors');
```

### Using namespaces

When creating vendors you can use namespaces.

Give your cache files the correct namespace, for example _MyVendor\Cache_.

Place your cache class files in location _src/Retrievers_ or _src/Cache_.

Add the location in your _ServiceProvider_ class in the _boot_ section.
```php
<?php

namespace Vendor\Package\Providers;

use Illuminate\Support\ServiceProvider;
use Nocs\Retriever\Support\Facades\Retriever;

class PackageServiceProvider extends ServiceProvider
{

    public function boot(Router $router)
    {

        Retriever::loadRetrieversFrom(__dir__.'/../Cache', 'package');

    }
}

```

Retrieve the value with the namespace prefixed (snaked) to the key:
```php
<?php

$value = retriever()->get('my_vendor::colors');
```

### Arguments

A second argument to the _get_ call can be provided as an array of arguments that will be passed to the cache closure.

### Short

Instead of using
```php
<?php

$value = retriever()->get('key');
$value = retriever()->get('key', 'default');
```
you can use 
```php
<?php

$value = retriever('key', 'default');
```

## Testing

To test, run
```sh
composer test
```

## Security

If you discover any security related issues, please email [the author](composer.json) instead of using the issue tracker.

## Credits
- [Nine O'Clock Somewhere](https://github.com/nineoclocksomewhere)
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[link-contributors]: ../../contributors