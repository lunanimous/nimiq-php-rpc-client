# Nimiq PHP Client

> PHP implementation of the Nimiq RPC client specs.

## About

[![Latest Stable Version](https://poser.pugx.org/lunanimous/php-rpc-client/v/stable)](https://packagist.org/packages/lunanimous/php-rpc-client)
![continuous integration](https://github.com/lunanimous/nimiq-php-rpc-client/workflows/continuous%20integration/badge.svg)

A Nimiq RPC client library in PHP. This client library implements the [Nimiq RPC specification](https://github.com/nimiq/core-js/wiki/JSON-RPC-API).

## Installation

The recommended way to install Nimiq PHP Client is with Composer. Composer is a dependency management tool for PHP that
allows you to declare the dependencies your project needs and installs them into your project.

```sh
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

You can add Nimiq PHP Client as a dependency using the composer.phar CLI:

```sh
php composer.phar require lunanimous/php-rpc-client
```

Alternatively, you can specify Guzzle as a dependency in your project's existing composer.json file:

```json
{
    "require": {
        "lunanimous/php-rpc-client": "~1.0"
    }
}
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can find out more on how to install Composer, configure autoloading, and other best-practices for defining dependencies at [getcomposer.org](https://getcomposer.org).

## Usage

You can send requests with to a Nimiq node using a `Lunanimous\Rpc\NimiqClient` object.

```php
$config = [
    'scheme' => 'http',
    'host' => '127.0.0.1',
    'port' => 8648,
    'user' => 'luna',
    'password' => 'moon',
    'timeout' => false,
];

$client = new \Lunanimous\Rpc\NimiqClient($config);
```

Once we have the client, we can start communicating with the Nimiq node. If no `$config` object is passed in constructor it will use same defaults as the Nimiq node defaults.

```php
$client = new \Lunanimous\Rpc\NimiqClient();
$blockNumber = $client->getBlockNumber();

echo $blockNumber;
```

To discover all methods that are available head over to [the documentation](https://github.com/lunanimous/nimiq-php-rpc-client/tree/master/docs).

## Documentation

The complete documentation is available in the `/docs` folder, you can also [view it here](https://github.com/lunanimous/nimiq-php-rpc-client/tree/master/docs).

You can also check out the [Nimiq RPC  specs](https://github.com/nimiq/core-js/wiki/JSON-RPC-API) which this client is compliant.

## License

[Apache 2.0](LICENSE.md)
