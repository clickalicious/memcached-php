Memcached.php
=============

Plain vanilla PHP `Memcached` client library with nearly full support of the `Memcached` protocol specification.

## Features

 - Nearly 100% of `Memcached` protocol specification covered
 - Clean & well documented code 
 - Unit-Tested
 - Support for storing all (8) PHP variable types 
 - Increment & Decrement support
 - Connection sharing  
 - Configurable connection close behavior

**Memcached.php** covers almost 100% of the `Memcached` protocol specification. The code base is clean, full documented and developed following the PSR coding standards (PSR-0/4, PSR-1, PSR-2). The code is unit-tested (PHPUnit) and the coverage is above 75%. The library supports storing of all (8) [PHP variable types](http://php.net/manual/en/language.types.intro.php "PHP's variable types"). It supports \<incr\> and \<decr\> command on stored integers. The [connection handling is done like recommended](https://github.com/memcached/memcached/blob/master/doc/protocol.txt#L10 "Keep connections open and share them via a pool across instances.") in the `Memcached` protocol specification.

## Purpose

This client is neither tested nor designed to be used in heavy load environments. It was designed and developed by me as a client library for my `phpMemAdmin` project. So I was able to remove dependencies of both `Memcache` + `Memcached` (PECL) extensions - which are btw a bit weired designed and both do a spooky mix of different responsibilities (like pool management in `Memcached` or `stats` layering in `Memcache`). I've tried to align 100% with the Memcached protocol specification. In some cases I didn't liked the naming convention of the `Memcached` protocol specification and so I created some proxies. As an example: I decided to implement increment() as proxy to incr() and decrement() as proxy to decr().

## PHP data types
PHP supports eight primitive types. And so this library supports those types as well. These types are  

four scalar types:

    boolean
    integer
    float (floating-point number, aka double)
    string

two compound types:

    array
    object

and finally two special types:

    resource
    NULL


## Installation

The recommended way to install this library is through [Composer](http://getcomposer.org/). Require the `clickalicious/memcached.php` package into your `composer.json` file:

```json
{
    "require": {
        "clickalicious/memcached.php": "dev-master"
    }
}
```

**Memcached.php** is also available as [download from github packed as zip-file](https://github.com/clickalicious/Memcached.php/archive/master.zip "zip package containing library for download") or via git clone: `git clone https://github.com/clickalicious/Memcached.php.git .`.

## Usage

Simple demonstration to get started:
 - Create a `client` instance and connect it (*lazy*) to `Memcached` daemon on host *127.0.0.1* (on default port)
 - Set *key* **foo** with *value* **1.00** 
 - Retrieve *value* for *key* **foo**

```php
$client = new \Clickalicious\Memcached\Client(
    '127.0.0.1' 
);  
$client->set('foo', 1.00);
// Returns 1.00 as double!     
$client->get('foo');   
``` 

## Demo

You will find a `Demo.php` showing in detail how to use the **Memcached.php** `client`.

## Flags

**Memcached.php** makes use of the clients` flags field. The first **16 Bits** (Decimal up to 65.535) are reserved by this client library. The bitmask (flags) is also propagated to the public API so you can use it too. But be aware of the reservation: You can start using Bit **17 - 32** (the field is 32 Bit unsigned as Decimal interpretation).

## Documentation

The best and currently only existing documentation is the inline documentation of this project. So please have a look at the source to understand how Memcached.php works internally.

## Tests

**Memcached.php** is unit tested and the coverage is above 75%. You will find a PHPUnit configuration including testsuites in directory `tests/`. 

To run tests you only need to execute the following command on `cli`:

```sh
phpunit -c tests/phpunit.xml
```

## Future

Friends I work a lot on this project :) In detail I'm working on:

 - `\Clickalicious\Memcached\Proxy`  
   This should become a proxy implementation which is able to act as `Memcache` or `Memcached` (both PECL) extension (emulate) for testing (primary mocking/stubbing). 
 - `\Clickalicious\Memcached\Server`  
   This should become a virtual (emulated) mode which emulates a complete `Memcached` backend.
 - Refactoring the really ugly parts (response parsing is a mess! yeah I know :( )
 - More Unit-Tests

If you are interested in any of these features too - please let me know. Maybe we can adjust the priority and speed things up ...

## Participate

... yeah. If you're a code monkey too - maybe we can build a force ;) If you would like to participate in either **Code**, **Comments**, **Documentation**, **Wiki**, **Bug-Reports**, **Unit-Tests**, **Bug-Fixes**, **Feedback** and/or **Critic** then please let me know as well!