# Palmtree ArgParser

[![License](http://img.shields.io/packagist/l/palmtree/argparser.svg)](LICENSE)

Parse an array of arguments and call matching setters and/or merge with default arguments.

## Requirements
* PHP >= 7.1

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/argparser
```

## Usage

```php
<?php

use Palmtree\ArgParser\ArgParser;

class SomeClass {
    public static $defaultArgs = [
        'force' => false,
    ];

    private $name;
    private $args = [];

    public function __construct($args = []) {
        $parser = new ArgParser($args);

        $parser->parseSetters($this);
        $this->args = $parser->resolveOptions(static::$defaultArgs);
    }

    public function setName($name) {
        $this->name = $name;
    }
}
```
```php
<?php
// Calls $obj->setName('Andy') and sets the force arg to true
$obj = new SomeClass([
    'name' => 'Andy',
    'force' => true,
]);
```

## License

Released under the [MIT license](LICENSE)
