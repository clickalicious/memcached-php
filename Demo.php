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
 * Memcached.php - Memcached (PECL) compatible client implementation in plain vanilla PHP.
 *
 * Copyright (c) 2014 - 2015, Benjamin Carl - All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - All advertising materials mentioning features or use of this software
 *   must display the following acknowledgement: This product includes software
 *   developed by Benjamin Carl and other contributors.
 * - Neither the name Benjamin Carl nor the names of other contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Please feel free to contact us via e-mail: opensource@clickalicious.de
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_Memcached_Php
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: $
 * @link       https://github.com/clickalicious/Memcached.php
 */

require_once 'Lib\Memcached_Php.php';

/**
 * Memcached.php
 *
 * Demonstration of Memcached.php Memcached Client.
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_Memcached_Php
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id: $
 * @link       https://github.com/clickalicious/Memcached.php
 */

// Create memcached instance ...
$memcached = new Memcached_Php(
    '127.0.0.1'
);

try {
    //$result = $memcached->set('number', 1);

    $memcached->set('testBen', [1,2,3]);
    $result = $memcached->get('testBen');

    //$memcached->increment('number', 2);
    //$memcached->decrement('number', 1);
    //$memcached->version();
    //$memcached->stats(Memcachedphp::STATS_TYPE_SLABS);
    //$memcached->get('foo', true);
    //$memcached->get('foo');
    //$memcached->set('foo', 'bar');
    //$memcached->add('foo', 'bar');
    //$memcached->replace('foo', 'baz');
    //$memcached->delete('foo');
    //$memcached->append('foo', 'baz');
    //$memcached->prepend('foo', 'baz');
    //$memcached->cas('1', 'foo', 'bar');

} catch (Exception $e) {
    $result = $e->getMessage();

}

echo '<pre>';
var_dump(
    $result
);
echo '</pre>';

die;
