<?php

/**
 * (The MIT license)
 * Copyright 2017 clickalicious, Benjamin Carl
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

namespace Clickalicious\Memcached\Php;

/**
 * Class UtilTest
 *
 * @package Clickalicious\Memcached\Php
 * @author  Benjamin Carl <opensource@clickalicious.de>
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * PHP version running on.
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
                'id'         => 2135,
                'first_name' => 'John',
                'last_name'  => 'Doe',
            ),
            array(
                'id'         => 3245,
                'first_name' => 'Sally',
                'last_name'  => 'Smith',
            ),
            array(
                'id'         => 5342,
                'first_name' => 'Jane',
                'last_name'  => 'Jones',
            ),
            array(
                'id'         => 5623,
                'first_name' => 'Peter',
                'last_name'  => 'Doe',
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
         * Array
         * (
         * [0] => John
         * [1] => Sally
         * [2] => Jane
         * [3] => Peter
         * )
         */
        $data = array_column_emulation($this->data, 'first_name');

        $this->assertContains('John', $data);
        $this->assertContains('Sally', $data);
        $this->assertContains('Jane', $data);
        $this->assertContains('Peter', $data);

        $this->assertArrayHasKey(0, $data);
        $this->assertArrayHasKey(1, $data);
        $this->assertArrayHasKey(2, $data);
        $this->assertArrayHasKey(3, $data);
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
         * Array
         * (
         * [2135] => Doe
         * [3245] => Smith
         * [5342] => Jones
         * [5623] => Doe
         * )
         */
        $data = array_column_emulation($this->data, 'first_name', 'id');

        $this->assertContains('John', $data);
        $this->assertContains('Sally', $data);
        $this->assertContains('Jane', $data);
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
         * Array
         * (
         * [2135] => Doe
         * [3245] => Smith
         * [5342] => Jones
         * [5623] => Doe
         * )
         */
        $data = array_column_emulation($this->data, 'foo');
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
         * Array
         * (
         * [2135] => Doe
         * [3245] => Smith
         * [5342] => Jones
         * [5623] => Doe
         * )
         */
        $data = array_column_emulation($this->data, 'first_name', 1);
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
         * Array
         * (
         * [2135] => Doe
         * [3245] => Smith
         * [5342] => Jones
         * [5623] => Doe
         * )
         */
        $data = array_column_emulation($this->data, 'first_name', 'foo');
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
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testArrayColumnEmulationErrorHandlingWrongSecondArgument()
    {
        array_column_emulation($this->data, new \stdClass());
    }

    /**
     * Test: array_column implementation: error handling.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testArrayColumnEmulationErrorHandlingWrongThirdArgument()
    {
        array_column_emulation($this->data, 'first_name', new \stdClass());
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
             * Array
             * (
             * [0] => John
             * [1] => Sally
             * [2] => Jane
             * [3] => Peter
             * )
             */
            $data = \Clickalicious\Memcached\Php\array_column($this->data, 'first_name');

            $this->assertContains('John', $data);
            $this->assertContains('Sally', $data);
            $this->assertContains('Jane', $data);
            $this->assertContains('Peter', $data);

            $this->assertArrayHasKey(0, $data);
            $this->assertArrayHasKey(1, $data);
            $this->assertArrayHasKey(2, $data);
            $this->assertArrayHasKey(3, $data);
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
        $this->assertTrue(\Clickalicious\Memcached\Php\boolval_emulation($data));

        $data = 1;
        $this->assertTrue(\Clickalicious\Memcached\Php\boolval_emulation($data));

        $data = 'TRUE';
        $this->assertTrue(\Clickalicious\Memcached\Php\boolval_emulation($data));

        $data = 'YES';
        $this->assertTrue(\Clickalicious\Memcached\Php\boolval_emulation($data));

        $data = 'Y';
        $this->assertTrue(\Clickalicious\Memcached\Php\boolval_emulation($data));

        $data = 'ON';
        $this->assertTrue(\Clickalicious\Memcached\Php\boolval_emulation($data));

        $data = '1';
        $this->assertTrue(\Clickalicious\Memcached\Php\boolval_emulation($data));

        $data = '-1';
        $this->assertTrue(\Clickalicious\Memcached\Php\boolval_emulation($data));

        $data = false;
        $this->assertFalse(\Clickalicious\Memcached\Php\boolval_emulation($data));
    }
}
