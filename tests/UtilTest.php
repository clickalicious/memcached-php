<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Memcached.php
 *
 * UtilTest.php - Unit tests for util functionality.
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

require_once CLICKALICIOUS_MEMCACHED_BASE_PATH . 'Clickalicious/Memcached/Util.php';

/**
 * Memcached.php
 *
 * Unit tests for util functionality.
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
class UtilTest extends PHPUnit_Framework_TestCase
{
    /**
     * The PHP version running on.
     *
     * @var string
     * @access protected
     */
    protected $phpVersion;

    /**
     * Controls if we need to test the array_column()
     *
     * @var bool
     * @access protected
     */
    protected $testProxy = true;

    /**
     * The test data we work on.
     *
     * @var array
     * @access protected
     */
    protected $data;


    /**
     * Prepare some stuff.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setUp()
    {
        $phpVersion = explode('.', PHP_VERSION);
        $phpVersion = $phpVersion[0] . $phpVersion[1];

        if ($phpVersion >= 5.5) {
            $this->testProxy = false;
        }

        // Test data
        $this->data = array(
            array(
                'id' => 2135,
                'first_name' => 'John',
                'last_name' => 'Doe',
            ),
            array(
                'id' => 3245,
                'first_name' => 'Sally',
                'last_name' => 'Smith',
            ),
            array(
                'id' => 5342,
                'first_name' => 'Jane',
                'last_name' => 'Jones',
            ),
            array(
                'id' => 5623,
                'first_name' => 'Peter',
                'last_name' => 'Doe',
            )
        );
    }

    /**
     * Test: array_column implementation.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testArrayColumnEmulation()
    {
        /**
        Array
        (
            [0] => John
            [1] => Sally
            [2] => Jane
            [3] => Peter
        )
        */
        $data = \Clickalicious\Memcached\array_column_emulation($this->data, 'first_name');

        $this->assertContains('John',  $data);
        $this->assertContains('Sally', $data);
        $this->assertContains('Jane',  $data);
        $this->assertContains('Peter', $data);

        $this->assertArrayHasKey(0,  $data);
        $this->assertArrayHasKey(1,  $data);
        $this->assertArrayHasKey(2,  $data);
        $this->assertArrayHasKey(3,  $data);
    }

    /**
     * Test: array_column implementation: id as index.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testArrayColumnEmulationCustomIndex()
    {
        /**
        Array
        (
            [2135] => Doe
            [3245] => Smith
            [5342] => Jones
            [5623] => Doe
        )
         */
        $data = \Clickalicious\Memcached\array_column_emulation($this->data, 'first_name', 'id');

        $this->assertContains('John',  $data);
        $this->assertContains('Sally', $data);
        $this->assertContains('Jane',  $data);
        $this->assertContains('Peter', $data);

        $this->assertArrayHasKey(2135, $data);
        $this->assertArrayHasKey(3245, $data);
        $this->assertArrayHasKey(5342, $data);
        $this->assertArrayHasKey(5623, $data);
    }

    /**
     * Test: array_column implementation: wrong id int as index.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testArrayColumnEmulationWrongColumnString()
    {
        /**
        Array
        (
            [2135] => Doe
            [3245] => Smith
            [5342] => Jones
            [5623] => Doe
        )
        */
        $data = \Clickalicious\Memcached\array_column_emulation($this->data, 'foo');
        $this->assertArrayHasKey(0, $data);
        $this->assertArrayHasKey(1, $data);
        $this->assertArrayHasKey(2, $data);
        $this->assertArrayHasKey(3, $data);
    }

    /**
     * Test: array_column implementation: custom int as index.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testArrayColumnEmulationCustomIndexInt()
    {
        /**
        Array
        (
            [2135] => Doe
            [3245] => Smith
            [5342] => Jones
            [5623] => Doe
        )
         */
        $data = \Clickalicious\Memcached\array_column_emulation($this->data, 'first_name', 1);
        $this->assertArrayHasKey(0, $data);
        $this->assertArrayHasKey(1, $data);
        $this->assertArrayHasKey(2, $data);
        $this->assertArrayHasKey(3, $data);
    }

    /**
     * Test: array_column implementation: wrong id string as index.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testArrayColumnEmulationCustomIndexString()
    {
        /**
        Array
        (
            [2135] => Doe
            [3245] => Smith
            [5342] => Jones
            [5623] => Doe
        )
        */
        $data = \Clickalicious\Memcached\array_column_emulation($this->data, 'first_name', 'foo');
        $this->assertArrayHasKey(0, $data);
        $this->assertArrayHasKey(1, $data);
        $this->assertArrayHasKey(2, $data);
        $this->assertArrayHasKey(3, $data);
    }

    /**
     * Test: array_column implementation: error handling.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException PHPUnit_Framework_Error
     */
    public function testArrayColumnEmulationErrorHandlingWrongSecondArgument()
    {
        \Clickalicious\Memcached\array_column_emulation($this->data, new stdClass());
    }

    /**
     * Test: array_column implementation: error handling.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException PHPUnit_Framework_Error
     */
    public function testArrayColumnEmulationErrorHandlingWrongThirdArgument()
    {
        \Clickalicious\Memcached\array_column_emulation($this->data, 'first_name', new stdClass());
    }

    /**
     * Test: Set a key value pair.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testArrayColumnProxy()
    {
        if ($this->testProxy === true) {
            /**
            Array
            (
                [0] => John
                [1] => Sally
                [2] => Jane
                [3] => Peter
            )
             */
            $data = \Clickalicious\Memcached\array_column($this->data, 'first_name');

            $this->assertContains('John',  $data);
            $this->assertContains('Sally', $data);
            $this->assertContains('Jane',  $data);
            $this->assertContains('Peter', $data);

            $this->assertArrayHasKey(0,  $data);
            $this->assertArrayHasKey(1,  $data);
            $this->assertArrayHasKey(2,  $data);
            $this->assertArrayHasKey(3,  $data);


        } else {
            $this->assertTrue(true);
        }
    }



    /**
     * Test: Set a key value pair.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    public function testBoolvalEmulation()
    {
        $data = true;
        $this->assertTrue(\Clickalicious\Memcached\boolval_emulation($data));

        $data = 1;
        $this->assertTrue(\Clickalicious\Memcached\boolval_emulation($data));

        $data = 'TRUE';
        $this->assertTrue(\Clickalicious\Memcached\boolval_emulation($data));

        $data = 'YES';
        $this->assertTrue(\Clickalicious\Memcached\boolval_emulation($data));

        $data = 'Y';
        $this->assertTrue(\Clickalicious\Memcached\boolval_emulation($data));

        $data = 'ON';
        $this->assertTrue(\Clickalicious\Memcached\boolval_emulation($data));

        $data = '1';
        $this->assertTrue(\Clickalicious\Memcached\boolval_emulation($data));

        $data = '-1';
        $this->assertTrue(\Clickalicious\Memcached\boolval_emulation($data));

        $data = false;
        $this->assertFalse(\Clickalicious\Memcached\boolval_emulation($data));
    }
}
