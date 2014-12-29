<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Memcache.php
 *
 * Demo.php - Unit tests for client functionality.
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
 * @subpackage Clickalicious_Memcached_Tests
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 */

require_once MEMCACHED_BASE_PATH . 'MemcachedPhp.php';

use \Clickalicious\MemcachedPhp\MemcachedPhp;

/**
 * Memcached.php
 *
 * Unit tests for client functionality.
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_Tests
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * The Host used for testing.
     *
     * @var string
     * @access protected
     */
    protected $host = '127.0.0.1';

    /**
     * Client instance
     *
     * @var \Clickalicious\MemcachedPhp\MemcachedPhp
     * @access protected
     */
    protected $client;

    /**
     * Key for test entries
     *
     * @var string
     * @access protected
     */
    protected $key;

    /**
     * Value for test entries
     *
     * @var string
     * @access protected
     */
    protected $value;


    /**
     * Prepare some stuff.
     */
    protected function setUp()
    {
        $this->key   = md5(microtime(true));
        $this->value = sha1($this->key);

        $this->client = new MemcachedPhp(
            $this->host
        );
    }

    /**
     * Test if a value could be set
     */
    public function testSet()
    {
        $this->assertTrue($this->client->set($this->key, $this->value));
    }

    /**
     * Test if a value could be get back
     */
    public function testGet()
    {
        $this->assertTrue($this->client->set($this->key, 1.01));
        $this->assertTrue(is_double($this->client->get($this->key)));
    }

    /**
     * Cleanup
     */
    protected function tearDown()
    {
        $this->client->delete($this->key);
    }
}
