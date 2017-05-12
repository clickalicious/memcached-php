<?php

/**
 * (The MIT license)
 * Copyright 2017 clickalicious, Benjamin Carl.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
 * BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
 * ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
require_once dirname(__DIR__).'/vendor/autoload.php';

use \Clickalicious\Memcached\Php\Client;

// Create memcached-php instance ...
$memcached = new Client('127.0.0.1');

// Some setup for randomized key(s) for demonstration ...
srand(microtime(true));
$dummy = md5(rand(1111, 9999));

// Try to do some stuff with memcached instance ...
try {
    $memcached->set($dummy, 1);
    $memcached->increment($dummy, 2);
    $memcached->increment($dummy, 2);
    $memcached->increment($dummy, 2);
    $memcached->decrement($dummy, 3);
    $memcached->increment($dummy, 1);

    $result = $memcached->get($dummy);

    $memcached->delete($dummy);
} catch (Exception $e) {
    $result = $e->getMessage();
}

echo sprintf('Result = "%s" (should be "5")', $result).PHP_EOL;
