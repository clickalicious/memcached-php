<img src="https://avatars0.githubusercontent.com/u/26927954?v=3&s=80" align="right" />

---

![Logo of memcached-php](docs/logo-large.png)

Plain vanilla PHP `Memcached` client library with full support of Memcached ASCII protocol.


| [![Build Status](https://travis-ci.org/clickalicious/memcached-php.svg?branch=master)](https://travis-ci.org/clickalicious/memcached-php) 	| [![Codacy branch grade](https://img.shields.io/codacy/grade/76a1648856b64c728b3e00c4954342ad/master.svg)](https://www.codacy.com/app/clickalicious/memcached-php?utm_source=github.com&utm_medium=referral&utm_content=clickalicious/memcached-php&utm_campaign=Badge_Grade) 	| [![Codacy coverage](https://img.shields.io/codacy/coverage/76a1648856b64c728b3e00c4954342ad.svg)](https://www.codacy.com/app/clickalicious/memcached-php?utm_source=github.com&utm_medium=referral&utm_content=clickalicious/webserver-daemon&utm_campaign=Badge_Grade) 	| [![clickalicious open-source](https://img.shields.io/badge/clickalicious-open--source-green.svg?style=flat)](https://www.clickalicious.de/) 	|
|---	|---	|---	|---	|
| [![GitHub release](https://img.shields.io/github/release/clickalicious/memcached-php.svg?style=flat)](https://github.com/clickalicious/memcached-php/releases) 	|  [![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://opensource.org/licenses/MIT) 	| [![Issue Stats](https://img.shields.io/issuestats/i/github/clickalicious/memcached-php.svg)](https://github.com/clickalicious/memcached-php/issues) 	| [![Dependency Status](https://dependencyci.com/github/clickalicious/memcached-php/badge)](https://dependencyci.com/github/clickalicious/memcached-php)   	|


## Table of Contents

- [Features](#features)
- [Example](#example)
- [Requirements](#requirements)
- [Philosophy](#philosophy)
- [Versioning](#versioning)
- [Roadmap](#roadmap)
- [Security-Issues](#security-issues)  
- [License »](LICENSE)  

## Features

 - ~ 100% of `Memcached` *ASCII*-protocol specification covered
 - Support for storing native PHP variable types (arrays, objects ...)
 - Increment & Decrement support
 - Efficient connection sharing  
 - Configurable connection close behavior 
 - High-quality & stable codebase (following PSR standards e.g. `PSR-1,2,4`)
 - Built on top of good PHP libraries
 - PHP >= 7.2 ready
 - Clean + well documented code
 - Unit-tested with a good coverage

**memcached-php** covers almost 100% of the `Memcached` protocol specification. The code is clean, full documented and developed following the PSR coding standards (PSR-0/4, PSR-1, PSR-2). The code is unit-tested (PHPUnit) and the coverage is high. The library supports \<incr\> and \<decr\> command on stored integers (strings) and the [connection handling is done like recommended](https://github.com/memcached/memcached/blob/master/doc/protocol.txt#L10 "Keep connections open and share them via a pool across instances.") in the `Memcached` protocol specification. Last but not least it supports seven of [PHP's eight variable types](http://php.net/manual/en/language.types.intro.php "PHP's variable types") - in detail four scalar types:

    boolean
    integer
    float (floating-point number [double])
    string

two compound types:

    array
    object

and finally one special type:

    NULL

So `resource` is the only type not supported.


## Example

 - Create a `Client` instance and connect it (*lazy*) to `Memcached` daemon on host *127.0.0.1* (on default port [11211])
 - Set *key* **foo** with *value* **1.00**
 - Retrieve *value* for *key* **foo**

```php
$client = new \Clickalicious\Memcached\Php\Client('127.0.0.1');

// Set a value of type float
$client->set('foo', 1.00);

// Returns 1.00 as PHP's type float!
$client->get('foo');
```
You will find a demonstration [`Demo.php` »](Demo.php) showing in detail how to use the **memcached-php** `client`.

## Requirements

 - `PHP >= 5.6` (compatible up to version `7.2` as well as `HHVM`)

## Philosophy

This client is neither tested nor designed to be used in heavy load environments. It was designed and developed by me as a client library for my [phpMemAdmin](https://github.com/clickalicious/phpMemAdmin "phpMemAdmin on github") project. So I was able to remove dependencies of both `Memcache` + `Memcached` (PECL) extensions - both are designed in a way i don't like. I've tried to align 100% with the Memcached protocol specification. In some cases I didn't liked the naming convention and so I created some proxies. As an example - I decided to implement increment() as proxy to incr() and decrement() as proxy to decr(). I will add some more responsibilities in some more classes like a [PSR compatible](https://github.com/php-fig/fig-standards/blob/master/proposed/cache.md "PSR Cache proposal") Caching proxy and a Pool/Cluster Class for management operations soon.

## Versioning

For a consistent versioning i decided to make use of `Semantic Versioning 2.0.0` http://semver.org. Its easy to understand, very common and known from many other software projects.

## Roadmap

- [ ] n.a.

[![Throughput Graph](https://graphs.waffle.io/clickalicious/memcached-php/throughput.svg)](https://waffle.io/clickalicious/memcached-php/metrics)


## Installation

The recommended way to install this library is through [Composer](http://getcomposer.org/). Require the `clickalicious/memcached-php` package into your `composer.json` file:

```json
{
    "require": {
        "clickalicious/memcachedphp": "^2.0"
    }
}
```

**memcached-php** is also available as [download from github packed as zip-file](https://github.com/clickalicious/memcached-php/archive/master.zip "zip package containing library for download") or via `git clone https://github.com/clickalicious/memcached-php.git .`


## Data

`Strings`, `Integers` and `Float-Values` are never modified by this library in any way. Those types will be stored by `Memcached`'s internal system - while all other types will be serialized by this client and can optionally be stored compressed (*LZW*/*Smaz*) - in one of the next releases of this library - targeting 0.4.0. I'm working on an PoC implementation of `Smaz - a short string compression library` (https://github.com/zhenhao/smaz.php) and on a german translation of the translation table used by `Smaz`.


## Metadata

`Memcached` provides a 32 Bit (Version > 1.2.1) unsigned Integer field for meta data. From the `Memcached` protocol specification:
> Note that in memcached 1.2.1 and higher, flags may be 32-bits, instead
of 16, but you might want to restrict yourself to 16 bits for
compatibility with older versions.

**memcached-php** uses this field for its meta data. The meta data is required to mark data for serialization and stuff like this. This meta data is stored via the clients` flags field. The lower first **8 Bits** (*lowest Byte*) are reserved by **memcached-php**. The other 8 Bits (half of the 16 Bits) can be used by your app.


## Documentation

The best and currently only existing documentation is the inline documentation of this project. So please have a look at the source to understand how **memcached-php** works internally.


## Tests

**memcached-php** is unit-tested and the code coverage is high. For an in-detail view have a look at this always up to date [Code Coverage report](http://clickalicious.github.io/memcached-php/dashboard.html "Code Coverage").

Running the Tests  
You will find a PHPUnit configuration including testsuites in directory `tests/`. To run those configuration execute the following command on `cli`:

```sh
phpunit -c tests/phpunit.xml --testdox
```


### Tests
The unit-tests are fired against an existing and real `Memcached` daemon. Please be aware that you need a running `Memcached` deamon on the host you run the unit-tests listening on the default port (11211). In Result the unit-tests are not that isolated cause they are bound to a running `Memcached` daemon and network as well.


## Versioning
For a consistent versioning i decided to make use of `Semantic Versioning 2.0.0` http://semver.org. Its easy to understand, very common and known from many other software projects.


## Roadmap

 - [ ] Hardening code - more stability!
 - [ ] `\Clickalicious\Memcached\Proxy`  
   This should become a proxy implementation which is able to act as `Memcache` or `Memcached` (both PECL) extension (emulate) for testing (primary mocking/stubbing).
 - [ ] `\Clickalicious\Memcached\Server`  
   This should become a virtual (emulated) mode which emulates a complete `Memcached` backend.
 - [ ] Add compression support to be able to manipulate PECL Memcached stored data (FastLZ, zlib, LZW)
 - [ ] Replace explodes and array operations for data anlysis with regular expressions.
 - [ ] Increase coverage and cover (currently unused compression classes) more parts.

If you are interested in any of these features too - please let me know. Maybe we can adjust the priority and speed things up ...


## Participate & share

... yeah. If you're a code monkey too - maybe we can build a force ;) If you would like to participate in either **Code**, **Comments**, **Documentation**, **Wiki**, **Bug-Reports**, **Unit-Tests**, **Bug-Fixes**, **Feedback** and/or **Critic** then please let me know as well!
<a href="https://twitter.com/intent/tweet?hashtags=&original_referer=http%3A%2F%2Fgithub.com%2F&text=%23memcached-php%20-%20Plain%20vanilla%20PHP%20%40Memcached%20client%20https%3A%2F%2Fgithub.com%2Fclickalicious%2Fmemcached-php&tw_p=tweetbutton" target="_blank">
  <img src="http://jpillora.com/github-twitter-button/img/tweet.png"></img>
</a>


## Sponsors  
Thanks to our sponsors and supporters:  

| JetBrains | Navicat |
|---|---|
| <a href="https://www.jetbrains.com/phpstorm/" title="PHP IDE :: JetBrains PhpStorm" target="_blank"><img src="https://resources.jetbrains.com/assets/media/open-graph/jetbrains_250x250.png" height="55"></img></a> | <a href="http://www.navicat.com/" title="Navicat GUI - DB GUI-Admin-Tool for MySQL, MariaDB, SQL Server, SQLite, Oracle & PostgreSQL" target="_blank"><img src="http://upload.wikimedia.org/wikipedia/en/9/90/PremiumSoft_Navicat_Premium_Logo.png" height="55" /></a>  |


###### Copyright
<div>Icons made by <a href="http://www.flaticon.com/authors/vectors-market" title="Vectors Market">Vectors Market</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a></div>