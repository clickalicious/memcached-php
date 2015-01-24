<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Memcached.php
 *
 * ClientTest.php - Unit tests for client functionality.
 *
 *
 * PHP versions 5.3
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
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 */

require_once CLICKALICIOUS_MEMCACHED_BASE_PATH . 'Clickalicious/Memcached/Client.php';

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
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
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
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
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
     * Test: Set a key value pair.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testSetAKeyValuePair()
    {
        $this->assertTrue($this->client->set($this->key, $this->value));
    }

    /**
     * Test: Get a value by key.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testGetAValueByKey()
    {
        // Test success (ask for existing key)
        $this->assertTrue($this->client->set($this->key, $this->value));
        $this->assertEquals(
            $this->value,
            $this->client->get($this->key)
        );

        // Test failure (ask for not existing key)
        $this->assertFalse($this->client->get(md5($this->key)));
    }

    /**
     * Test: Add a key value pair.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testAddAKeyValuePair()
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
     * Test: Replace an existing value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testReplaceAnExistingValue()
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
     * Test: Append a value to an existing one.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testAppendAValueToAnExistingOne()
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
     * Test: Prepend a value to an existing one.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testPrependAValueToAnExistingOne()
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
     * Test: Cas set a key value pair.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testCasSetAKeyValuePair()
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

        $this->assertFalse($this->client->cas($value[$this->key]['meta']['cas'], $this->key, $this->value));

        $this->assertEquals(
            'bar',
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Send a valid custom command string.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testSendAValidCustomCommandString()
    {
        $testCommand = Client::COMMAND_VERSION . Client::COMMAND_TERMINATOR;

        $this->assertRegExp(
            '/\d[\.]\d[\.]\d[\-\w]+/u',
            $this->client->send(Client::COMMAND_VERSION, $testCommand)
        );
    }

    /**
     * Test: Send an invalid command string.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException \Clickalicious\Memcached\Exception
     */
    public function testSendAnInvalidCustomCommandString()
    {
        $testCommand = 'foo' . Client::COMMAND_TERMINATOR;
        $this->client->send('foo', $testCommand);
        $this->setHost('128.0.0.1');
        $testCommand = Client::COMMAND_VERSION . Client::COMMAND_TERMINATOR;
        $this->client->send(Client::COMMAND_VERSION, $testCommand);
    }


    /**
     * Test: Retrieve version.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testRetrieveVersion()
    {
        $this->assertRegExp(
            '/\d[\.]\d[\.]\d[\-\w]+/u',
            $this->client->version()
        );
    }

    /**
     * Test: Storing PHP type string.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testStoringPhpTypeString()
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
     * Test: Storing PHP type float.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testStoringPhpTypeFloat()
    {
        $value = 5.23;

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertTrue(is_float($this->client->get($this->key)));
        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Storing PHP type integer.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testStoringPhpTypeInteger()
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
     * Test: Storing PHP type array.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testStoringPhpTypeArray()
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
     * Test: Storing PHP type object.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testStoringPhpTypeObject()
    {
        $value = new \stdClass();
        $value->{$this->key} = $this->value;

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertTrue(is_object($this->client->get($this->key)));
        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Storing PHP type null.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testStoringPhpTypeNull()
    {
        $value = null;

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertTrue(is_null($this->client->get($this->key)));
        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: Storing PHP type boolean.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testStoringPhpTypeBoolean()
    {
        $value = true;

        $this->assertTrue($this->client->set($this->key, $value));
        $this->assertTrue(is_bool($this->client->get($this->key)));
        $this->assertEquals(
            $value,
            $this->client->get($this->key)
        );
    }

    /**
     * Test: <increment> a stored value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @depends testStoringPhpTypeInteger
     */
    public function testIncrementAStoredValue()
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
     * Test: <decrement> a stored value.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @depends testStoringPhpTypeInteger
     */
    public function testDecrementAStoredValue()
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
     * Test: Connection - real with success as well as failure.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException \Clickalicious\Memcached\Exception
     */
    public function testConnectToAMemcachedDaemon()
    {
        $this->assertTrue(
            is_resource(
                $this->client->connect($this->host, Client::DEFAULT_PORT)
            )
        );

        // Now connect to a fake host/port with little timeout - just to get the exception tested
        $this->client->connect('1.2.3.4', '11211', 1);
    }

    /**
     * Test: Retrieve Stats from memcached daemon.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testRetrieveStats()
    {
        $stats = $this->client->stats();

        $this->assertTrue($this->client->set($this->key, $this->value));
        $this->assertEquals(
            $this->value,
            $this->client->get($this->key)
        );

        # Mostly the first key
        $this->assertArrayHasKey(
            'pid',
            $stats
        );

        # Mostly the last key
        $this->assertArrayHasKey(
            'evictions',
            $stats
        );

        $stats = $this->client->stats(Client::STATS_TYPE_ITEMS);

        $this->assertArrayHasKey(
            'items',
            $stats
        );

        $stats = $this->client->stats(Client::STATS_TYPE_SLABS);

        $this->assertArrayHasKey(
            'active_slabs',
            $stats
        );

        $this->assertGreaterThanOrEqual(
            1,
            $stats['active_slabs']
        );

        $slabs = $stats['active_slabs'];

        $cachedump = array();

        for ($i = 1; $i <= $slabs; ++$i) {
            $cachedumpTemp = $this->client->stats(
                Client::STATS_TYPE_CACHEDUMP,
                $i,
                Client::CACHEDUMP_ITEMS_MAX
            );

            $cachedump = array_merge_recursive(
                $cachedump,
                $cachedumpTemp
            );
        }

        $this->assertArrayHasKey(
            $this->key,
            $cachedump
        );
    }

    /**
     * Test: Trigger and handle ERROR.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException \Clickalicious\Memcached\Exception
     */
    public function testTriggerAndHandleError()
    {
        $this->client->send(Client::COMMAND_PHPUNIT, Client::COMMAND_PHPUNIT . Client::COMMAND_TERMINATOR);
    }

    /**
     * Test: Trigger and handle CLIENT ERROR.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException \Clickalicious\Memcached\Exception
     */
    public function testTriggerAndHandleClientError()
    {
        $this->client->send(Client::COMMAND_PHPUNIT, Client::COMMAND_PHPUNIT . "\r" . Client::COMMAND_TERMINATOR);
    }

    /**
     * Test: Trigger and handle SERVER ERROR.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException \Clickalicious\Memcached\Exception
     */
    public function testTriggerAndHandleServerError()
    {
        $this->client->send(Client::COMMAND_PHPUNIT, Client::COMMAND_PHPUNIT . "\r" . Client::COMMAND_TERMINATOR);
    }

    /**
     * Cleanup after single test. Remove the key created for tests.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function tearDown()
    {
        $this->client->delete($this->key);
    }
}
