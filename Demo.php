<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Memcache.php
 *
 * Demo.php - Demonstration of memcached-php Memcached Client.
 *
 *
 * PHP versions 5.3
 *
 * LICENSE:
 * memcached-php - Plain vanilla PHP Memcached client with full support of Memcached protocol.
 *
 * Copyright (c) 2014 - 2015, Benjamin Carl
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * - Neither the name of memcached-php nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_Demo
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/memcached-php
 */

/**
 * THE FOLLOWING REQUIRE IS ONLY REQUIRED AND RECOMMENDED IN DEVELOPMENT/FOR DEVELOPMENT OF Rng
 * IT DOES NOT ONLY INSTALL AN ADDITIONAL AUTOLOADER (IN ADDITION TO COMPOSER) IT ALSO ADJUST
 * THE DEBUG SETTINGS, ERROR-REPORTING AND THINGS LIKE THAT! SO DO NOT BOOTSTRAP IN PRODUCTION!
 */
require_once 'src/Clickalicious/Memcached/Bootstrap.php';

use Clickalicious\Memcached\Client;

/**
 * memcached-php
 *
 * Demonstration of memcached-php Memcached Client.
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_Demo
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/memcached-php
 */

// Create memcached-php instance ...
$memcached = new Client(
    '127.0.0.1'
);

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

echo '<pre>';
echo '<h1>Simple Demonstration</h1>';
echo 'Result should be "5":<br />';
echo $result;
echo '</pre>';
