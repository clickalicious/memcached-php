<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Memcache.php
 *
 * Demo.php - Demonstration of Memcached.php Memcached Client.
 *
 *
 * PHP versions 5
 *
 * LICENSE:
 * Memcached.php - Plain vanilla PHP Memcached client with full support of Memcached protocol.
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
 * - Neither the name of Memcached.php nor the names of its
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
 * @link       https://github.com/clickalicious/Memcached.php
 */

require_once 'Lib\Clickalicious\Memcached\Client.php';

use Clickalicious\Memcached\Client;

/**
 * Memcached.php
 *
 * Demonstration of Memcached.php Memcached Client.
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_Demo
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 */

// Create Memcached.php instance ...
$memcached = new Client(
    '127.0.0.1'
);

// Some setup for randomized key(s) for demonstration ...
srand(microtime(true));
$dummy = md5(rand(1111, 9999));

// Try to do some stuff with memcached instance ...
try {

    /*
    // Fetch all keys and all values ...
    $allSlabs  = $memcached->stats(Client::STATS_TYPE_SLABS);
    $items     = $memcached->stats(Client::STATS_TYPE_ITEMS);

    foreach ($allSlabs as $server => $slabs) {

        if (isset($slabs['active_slabs']) === true) {
            unset($slabs['active_slabs']);
        }

        if (isset($slabs['total_malloced']) === true) {
            unset($slabs['total_malloced']);
        }

        foreach ($slabs AS $slabId => $slabMeta) {
            $cachedump = $memcached->stats(
                Client::STATS_TYPE_CACHEDUMP,
                (int)$slabId,
                Client::CACHEDUMP_ITEMS_MAX
            );

            foreach($cachedump as $serverToo => $arrVal) {
                foreach($arrVal as $k => $v) {
                    $fetched = sprintf('Fetched key "%s" with value "%s"', $k, $memcached->get($k));
                    echo $fetched . PHP_EOL;
                }
            }
        }
    }
    die;
    */

    $memcached->set($dummy, 1);
    $memcached->increment($dummy, 2);
    $memcached->increment($dummy, 2);
    $memcached->increment($dummy, 2);
    $memcached->decrement($dummy, 3);

    $result = $memcached->get($dummy);

    $memcached->delete($dummy);

} catch (Exception $e) {
    $result = $e->getMessage();

}

echo '<pre>';
echo '<h1>Simple Demonstration</h1>';
echo 'Result should be (int)4:<br />';
var_dump(
    $result
);
echo '</pre>';
