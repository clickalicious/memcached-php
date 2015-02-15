<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Clickalicious\Memcached;

/**
 * Memcached.php
 *
 * Util.php - A utility include file which provides some emulated functions of PHP releases
 * > 5.3. array_column() for example.
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
 * @subpackage Clickalicious_Memcached_Util
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 */

/*----------------------------------------------------------------------------------------------------------------------
| General Tools & Helper
+---------------------------------------------------------------------------------------------------------------------*/

/**
 * Copyright (c) 2013 Ben Ramsey <http://benramsey.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Ben Ramsey <http://benramsey.com>
 * @copyright 2013 Ben Ramsey
 * @license http://opensource.org/licenses/MIT MIT
 * @codeCoverageIgnore
 */
if (false === function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned array by the values from the
     * $indexKey column in the input array.
     *
     * @param array $input      A multi-dimensional array (record set) from which to pull a column of values.
     * @param mixed $column_key The column of values to return. This value may be the integer key of the column you
     *                          wish to retrieve, or it may be the string key name for an associative array.
     * @param mixed $index_key  (Optional.) The column to use as the index/keys for the returned array. This value
     *                          may be the integer key of the column, or it may be the string key name.
     * @return array
     * @codeCoverageIgnore
     */
    function array_column(array $input, $column_key, $index_key = null)
    {
        /**
         * Proxy call to array_column_emulation(): makes it a bit cleaner and testing on all systems possible
         */
        return array_column_emulation($input, $column_key, $index_key);
    }
}

/**
 * Returns the values from a single column of the input array, identified by the $columnKey.
 *
 * Optionally, you may provide an $indexKey to index the values in the returned array by the values from the
 * $indexKey column in the input array.
 *
 * @param array $input      A multi-dimensional array (record set) from which to pull a column of values.
 * @param mixed $column_key The column of values to return. This value may be the integer key of the column you
 *                          wish to retrieve, or it may be the string key name for an associative array.
 * @param mixed $index_key  (Optional.) The column to use as the index/keys for the returned array. This value
 *                          may be the integer key of the column, or it may be the string key name.
 * @return array
 */
function array_column_emulation(array $input, $column_key, $index_key = null)
{
    // Check for ...
    if (!is_int($column_key)    &&
        !is_float($column_key)  &&
        !is_string($column_key) &&
        $column_key !== null    &&
        !(
            is_object($column_key) && method_exists($column_key, '__toString')
        )
    ) {
        return !trigger_error(
            'array_column(): The column key should be either a string or an integer',
            E_USER_WARNING
        );
    }

    // Check for ...
    if (null !== $index_key     &&
        !is_int($index_key)     &&
        !is_float($index_key)   &&
        !is_string($index_key)  &&
        !(
            is_object($index_key) && method_exists($index_key, '__toString')
        )
    ) {
        return !trigger_error(
            'array_column(): The index key should be either a string or an integer',
            E_USER_WARNING
        );
    }

    // Check for passed index key
    if (null !== $index_key) {
        if (is_float($index_key) || is_int($index_key)) {
            $index_key = (int)$index_key;
        } else {
            $index_key = (string)$index_key;
        }
    }

    $result = array();

    foreach ($input as $row) {
        $key    = null;
        $keySet = false;

        if (null !== $index_key && true === is_array($row) && true === array_key_exists($index_key, $row)) {
            $keySet = true;
            $key    = (string)$row[$index_key];
        }

        if (true === is_array($row) && true === array_key_exists($column_key, $row)) {
            $value = $row[$column_key];
        } else {
            $value = $row;
        }

        if ($keySet === true) {
            $result[$key] = $value;
        } else {
            $result[]     = $value;
        }
    }

    return $result;
}

/**
 * Alternative implementation of boolval() for PHP releases < 5.5.
 *
 * @param mixed $boolean The potential boolean
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return bool TRUE || FALSE depending on input.
 * @access public
 * @codeCoverageIgnore
 */
if (false === function_exists('boolval')) {

    /**
     * Alternative implementation of boolval() for PHP releases < 5.5.
     *
     * @param mixed $boolean The potential boolean
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE || FALSE depending on input.
     * @access public
     * @codeCoverageIgnore
     */
    function boolval($boolean)
    {
        /**
         * Proxy call to boolval_emulation()
         */
        return boolval_emulation($boolean);
    }
}

/**
 * Alternative implementation of boolval() for PHP releases < 5.5.
 *
 * @param mixed $boolean The potential boolean
 *
 * @author Benjamin Carl <opensource@clickalicious.de>
 * @return bool TRUE || FALSE depending on input.
 * @access public
 */
function boolval_emulation($boolean)
{
    if (is_string($boolean) === true) {
        $boolean = strtoupper($boolean);
    }

    if (true === in_array($boolean, array(true, 1, 'TRUE', 'YES', 'Y', 'ON', '1'), true)) {
        return true;
    }

    // At least let PHP decide :)
    return $boolean ? true : false;
}
