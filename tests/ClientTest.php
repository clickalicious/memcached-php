<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Memcached.php
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

require_once CLICKALICIOUS_MEMCACHED_BASE_PATH . 'Clickalicious\Memcached\Client.php';

use \Clickalicious\Memcached\Client;

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
     * @var \Clickalicious\Memcached\Client
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

        $this->client = new Client(
            $this->host
        );
    }

    /**
     * Test: Setting a value
     */
    public function testSet()
    {
        $this->assertTrue($this->client->set($this->key, $this->value));
    }

    /**
     * Test: Setting a value and retrieve it back
     */
    public function testGet()
    {
        $this->assertTrue($this->client->set($this->key, $this->value));
        $this->assertEquals(
            $this->value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Adding a value
     */
    public function testAdd()
    {
        // Should here return TRUE cause key does not exist
        $this->assertTrue($this->client->add($this->key, $this->value));
        $this->assertEquals(
            $this->value,
            $this->client->get($this->key)
        );
        // Should now return FALSE cause key already exists
        $this->assertFalse($this->client->add($this->key, $this->value));
    }

    /**
     * Test: Replacing a value
     */
    public function testReplace()
    {
        srand(microtime(true));
        $value = md5(rand(1, 65535));

        $this->assertFalse($this->client->replace($this->key, $value));

        // Should here return TRUE cause key does not exist
        $this->assertTrue($this->client->set($this->key, $this->value));
        $this->assertTrue($this->client->replace($this->key, $value));

        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Appending a value
     */
    public function testAppend()
    {
        srand(microtime(true));
        $value = md5(rand(1, 65535));

        $this->assertFalse($this->client->append($this->key, $value));

        // Should here return TRUE cause key does not exist
        $this->assertTrue($this->client->set($this->key, $this->value));
        $this->assertTrue($this->client->append($this->key, $value));

        $this->assertEquals(
            $this->value . $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Prepending a value
     */
    public function testPrepend()
    {
        srand(microtime(true));
        $value = md5(rand(1, 65535));

        $this->assertFalse($this->client->prepend($this->key, $value));

        $this->assertTrue($this->client->set($this->key, $this->value));
        $this->assertTrue($this->client->prepend($this->key, $value));

        $this->assertEquals(
            $value . $this->value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Command CAS
     */
    public function testCas()
    {
        // Random 32 Bit decimal (wrong CAS emulate)
        srand(microtime(true));
        $value = rand(0, 65535);

        $this->assertFalse($this->client->cas($value, $this->key, $value));

        $this->assertTrue($this->client->set($this->key, $this->value));

        $value = $this->client->gets(array($this->key), true);

        $this->assertTrue($this->client->cas($value[$this->key]['meta']['cas'], $this->key, 'bar'));

        $this->assertArrayHasKey(
            $this->key,
            $this->client->gets(array($this->key))
        );

        $this->assertEquals(
            'bar',
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Send command (Freestyle) to daemon.
     * @throws \Clickalicious\Memcached\Exception
     */
    public function testSend()
    {
        $testCommand = Client::COMMAND_VERSION . Client::COMMAND_TERMINATOR;

        $this->assertRegExp(
            '/\d[\.]\d[\.]\d[\-\w]+/u',
            $this->client->send(Client::COMMAND_VERSION, $testCommand)
        );
    }

    /**
     * Test: Command version
     */
    public function testVersion()
    {
        $this->assertRegExp(
            '/\d[\.]\d[\.]\d[\-\w]+/u',
            $this->client->version()
        );
    }

    /**
     * Test: Send wrong command (Freestyle) to daemon.
     * @expectedException \Clickalicious\Memcached\Exception
     */
    public function testSendWrongCommand()
    {
        $testCommand = 'foo' . Client::COMMAND_TERMINATOR;
        $this->client->send('foo', $testCommand);
        $this->setHost('128.0.0.1');
        $testCommand = Client::COMMAND_VERSION . Client::COMMAND_TERMINATOR;
        $this->client->send(Client::COMMAND_VERSION, $testCommand);
    }

    /**
     * Test: Handling of string values
     */
    public function testString()
    {
        $value = 'Hello World!';

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertTrue(is_string($this->client->get($this->key)));
        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Handling of double values
     */
    public function testDouble()
    {
        $value = 5.23;

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertTrue(is_double($this->client->get($this->key)));
        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Handling of integer values
     */
    public function testInteger()
    {
        $value = 523;

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertTrue(is_int($this->client->get($this->key)));
        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Handling of increment
     * @depends testInteger
     */
    public function testIncrement()
    {
        $value = 523;

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertEquals($value + 2, $this->client->increment($this->key, 2));
        $this->assertEquals($value + 4, $this->client->incr($this->key, 2));
        $this->assertEquals(
            $value + 4,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Handling of decrement
     * @depends testInteger
     */
    public function testDecrement()
    {
        $value = 525;

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertEquals($value - 2, $this->client->decrement($this->key, 2));
        $this->assertEquals($value - 4, $this->client->decr($this->key, 2));
        $this->assertEquals(
            $value - 4,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Handling of array values
     */
    public function testArray()
    {
        $value = array(
            5,
            23,
        );

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertTrue(is_array($this->client->get($this->key)));
        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Cleanup
     */
    protected function tearDown()
    {
        $this->client->delete($this->key);
    }
}
