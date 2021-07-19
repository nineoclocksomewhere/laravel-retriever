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

Define public non-static methods which return the cached values.

### Basic usage

Example:
_app/Cache/Colors.php_
```
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
```
<?php

$value = retriever()->get('colors.red');
```

### Using one method

When using only one method, you can use the _get_ method.
_app/Cache/Colors.php_
```
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
```
<?php

$value = retriever()->get('colors');
```

### Using namespaces

When creating vendors you can use namespaces.

Give your cache files the correct namespace, for example _MyVendor\Cache_.

Retrieve the value with the namespace prefixed (snaked) to the key:
```
<?php

$value = retriever()->get('my_vendor::colors');
```

### Defaults

A second argument can be provided to the _get_ call to define as a default return value when no cache value was found.

### Short

Instead of using
```
<?php

$value = retriever()->get('key');
$value = retriever()->get('key', 'default');
```
you can use 
```
<?php

$value = retriever('key', 'default');
```

## Testing

To test, run
```sh
composer test
```
