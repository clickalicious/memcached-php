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
 * @author    Ben Ramsey <http://benramsey.com>
 * @copyright 2013 Ben Ramsey
 * @license   http://opensource.org/licenses/MIT MIT
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
    if (!is_int($column_key) &&
        !is_float($column_key) &&
        !is_string($column_key) &&
        $column_key !== null &&
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
    if (null !== $index_key &&
        !is_int($index_key) &&
        !is_float($index_key) &&
        !is_string($index_key) &&
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
        $key = null;
        $keySet = false;

        if (null !== $index_key && true === is_array($row) && true === array_key_exists($index_key, $row)) {
            $keySet = true;
            $key = (string)$row[$index_key];
        }

        if (true === is_array($row) && true === array_key_exists($column_key, $row)) {
            $value = $row[$column_key];
        } else {
            $value = $row;
        }

        if ($keySet === true) {
            $result[$key] = $value;
        } else {
            $result[] = $value;
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
