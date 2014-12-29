<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Clickalicious\MemcachedPhp;

/**
 * Memcached.php
 *
 * MemcachedPhp.php - Plain vanilla PHP Memcached client with full support of Memcached protocol.
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
 * @subpackage Clickalicious_Memcached_MemcachedPhp
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 */

require_once 'Memcached/Exception.php';

/**
 * Memcached.php
 *
 * Plain vanilla PHP Memcached client with full support of Memcached protocol.
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_MemcachedPhp
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 */
class MemcachedPhp
{
    /**
     * The persistent ID of the instance for sharing connections via static!
     *
     * @var string
     * @access protected
     */
    protected $persistentId;

    /**
     * The hosts maintained within this instance
     *
     * @var array
     * @access protected
     */
    protected $hosts = array();

    /**
     * The Memcached daemons host.
     *
     * @var string
     * @access protected
     */
    protected $host;

    /**
     * The Memcached daemons port.
     *
     * @var string
     * @access protected
     */
    protected $port;

    /**
     * All open connections
     *
     * @var array
     * @access protected
     */
    protected static $connections = array();

    /**
     * Last result
     *
     * @var int
     * @access protected
     */
    protected $lastResult = 0;

    /**
     * Signals for transfer ended. Send as terminator by Memcached instance.
     *
     * @var array
     * @access protected
     */
    protected $sigsEnd = array(
        self::RESPONSE_END,
        self::RESPONSE_DELETED,
        self::RESPONSE_NOT_FOUND,
        self::RESPONSE_OK,
        self::RESPONSE_EXISTS,
        self::RESPONSE_ERROR,
        self::RESPONSE_RESET,
        self::RESPONSE_STORED,
        self::RESPONSE_NOT_STORED,
        self::RESPONSE_VERSION,
    );

    /**
     * A VALUE intro - used to detect VALUES response of get() & gets().
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_VALUE = 'VALUE';

    /**
     * A STAT intro - used to detect STAT response of <stats>.
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_STAT = 'STAT';

    /**
     * A STAT VALUE intro - used to detect STAT VALUE response of <stats>.
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_ITEM = 'ITEM';

    /**
     * Response END
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_END = 'END';

    /**
     * Response DELETED
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_DELETED = 'DELETED';

    /**
     * Response NOT_FOUND
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_NOT_FOUND = 'NOT_FOUND';

    /**
     * Response OK
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_OK = 'OK';

    /**
     * Response EXISTS
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_EXISTS = 'EXISTS';

    /**
     * Response ERROR
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_ERROR = 'ERROR';

    /**
     * Response RESET
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_RESET = 'RESET';

    /**
     * Response STORED
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_STORED = 'STORED';

    /**
     * Response NOT_STORED
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_NOT_STORED = 'NOT_STORED';

    /**
     * Response VERSION
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_VERSION = 'VERSION';

    /**
     * Response CLIENT_ERROR
     *
     * @var string
     * @access public
     * @const
     */
    const RESPONSE_CLIENT_ERROR = 'CLIENT_ERROR';

    /**
     * A collection of allowed commands which can be send to Memcached instance.
     *
     * @var array
     * @access protected
     */
    protected $allowedCommands = array(
        self::COMMAND_SET,
        self::COMMAND_ADD,
        self::COMMAND_REPLACE,
        self::COMMAND_APPEND,
        self::COMMAND_PREPEND,
        self::COMMAND_CAS,
        self::COMMAND_INCR,
        self::COMMAND_DECR,
        self::COMMAND_GET,
        self::COMMAND_GETS,
        self::COMMAND_DELETE,
        self::COMMAND_TOUCH,
        self::COMMAND_VERSION,
        self::COMMAND_STATS,
    );

    /**
     * The command for setting a key value pair to a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_SET = 'set';

    /**
     * Command for adding data to if not already exists.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_ADD = 'add';

    /**
     * Command for replacing a value with another one.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_REPLACE = 'replace';

    /**
     * Command for append data to existing data of an existing key.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_APPEND = 'append';

    /**
     * Command for prepend data to existing data of an existing key.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_PREPEND = 'prepend';

    /**
     * Command for cas.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_CAS = 'cas';

    /**
     * Command for incr.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_INCR = 'incr';

    /**
     * Command for decr.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_DECR = 'decr';

    /**
     * Command for retrieving a key and its value from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_GET = 'get';

    /**
     * Command for retrieving multiple keys and the values from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_GETS = 'gets';

    /**
     * The command for deleting a key value pair from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_DELETE = 'delete';

    /**
     * The command for touching a key value pair from a Memcached instance to change expiration time.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_TOUCH = 'touch';

    /**
     * The command for retrieving the version from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_VERSION = 'version';

    /**
     * The command for retrieving the stats from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_STATS = 'stats';

    /**
     * The command for retrieving the stats settings from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_STATS_SETTINGS = 'settings';

    /**
     * The command for retrieving the stats items from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_STATS_ITEMS = 'items';

    /**
     * The command for retrieving the stats slabs from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_STATS_SLABS = 'slabs';

    /**
     * The command for retrieving the stats reset from a Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_STATS_RESET = 'reset';

    /**
     * Command for retrieving stats sizes.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_STATS_SIZES = 'sizes';

    /**
     * Command for retrieving stats conns.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_STATS_CONNS = 'conns';

    /**
     * Command for retrieving stats cachedump.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_STATS_CACHEDUMP = 'cachedump';

    /**
     * The settings stats type.
     *
     * @var string
     * @access public
     * @const
     */
    const STATS_TYPE_SETTINGS = self::COMMAND_STATS_SETTINGS;

    /**
     * The items stats type.
     *
     * @var string
     * @access public
     * @const
     */
    const STATS_TYPE_ITEMS = self::COMMAND_STATS_ITEMS;

    /**
     * The slabs stats type.
     *
     * @var string
     * @access public
     * @const
     */
    const STATS_TYPE_SLABS = self::COMMAND_STATS_SLABS;

    /**
     * The reset stats type.
     *
     * @var string
     * @access public
     * @const
     */
    const STATS_TYPE_RESET = self::COMMAND_STATS_RESET;

    /**
     * The conns stats type.
     *
     * @var string
     * @access public
     * @const
     */
    const STATS_TYPE_CONNS = self::COMMAND_STATS_CONNS;

    /**
     * The cachedump stats type.
     *
     * @var string
     * @access public
     * @const
     */
    const STATS_TYPE_CACHEDUMP = self::COMMAND_STATS_CACHEDUMP;

    /**
     * The sizes stats type.
     *
     * @var string
     * @access public
     * @const
     */
    const STATS_TYPE_SIZES = self::COMMAND_STATS_SIZES;

    /**
     * Number of bytes fetched in one cycle from socket.
     *
     * @var int
     * @access public
     * @const
     */
    const SOCKET_READ_FETCH_BYTES = 256;

    /**
     * Maximum items to fetch if not overridden.
     *
     * @var int
     * @access public
     * @const
     */
    const CACHEDUMP_ITEMS_MAX = PHP_INT_MAX;

    /**
     * The default port of a host added.
     *
     * @var int
     * @access public
     * @const
     */
    const DEFAULT_PORT = 11211;

    /**
     * The separator for building commandline for Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_SEPARATOR  = ' ';

    /**
     * The terminator used to terminate a commandline send to Memcached instance.
     *
     * @var string
     * @access public
     * @const
     */
    const COMMAND_TERMINATOR = "\r\n";

    /**
     * The default and generic Memcached error.
     *
     * @var string
     * @access public
     * @const
     */
    const ERROR = 'ERROR';

    /**
     * The Memcached error for client error.
     *
     * @var string
     * @access public
     * @const
     */
    const ERROR_CLIENT = 'CLIENT_ERROR';

    /**
     * The Memcached error for server error.
     *
     * @var string
     * @access public
     * @const
     */
    const ERROR_SERVER = 'SERVER_ERROR';

    /**
     * The default bitmask to detect serialization support.
     *
     * @var int
     * @access public
     * @const
     */
    const DEFAULT_BITMASK = 1;

    /**
     * Memcached Constant Values
     */
    const RESULT_SUCCESS = 0;
    const RESULT_FAILURE = 1;
    const RESULT_DATA_EXISTS = 12;
    const RESULT_NOTSTORED = 14;
    const RESULT_NOTFOUND = 16;


    /**
     * Constructor.
     *
     * @param string $host         The host name this instance works on
     * @param int    $port         The port to connect to.
     * @param string $persistentId By default the Memcached instances are destroyed at the end of the request.
     *                             To create an instance that persists between requests, use persistent_id to specify a
     *                             unique ID for the instance. All instances created with the same persistent_id will
     *                             share the same connection.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return MemcachedPhp
     * @access public
     */
    public function __construct(
        $host         = null,
        $port         = self::DEFAULT_PORT,
        $persistentId = null
    ) {
        // Extract host and port from host string
        if ($host !== null) {
            $this
                ->host($host)
                ->port($port);
        }

        // Generate persistent Id if not passed
        if ($persistentId === null) {
            srand(time());
            $persistentId = sha1(rand(0, 99999999));
        }

        // Prepare connections for this instance' persistent Id if not already set
        if (isset(self::$connections[$persistentId]) === false) {
            self::$connections[$persistentId] = array();
        }

        // Execute fluent stuff
        $this
            ->persistentId($persistentId);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Public API (API, Helper, Setter & Getter
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for host.
     *
     * @param string $host The host to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Setter for host.
     *
     * @param string $host The host to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function host($host)
    {
        $this->setHost($host);
        return $this;
    }

    /**
     * Getter for host.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The host if set, otherwise NULL.
     * @access public
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Setter for port.
     *
     * @param string $port The port to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access public
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Setter for port.
     *
     * @param string $port The port to set.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access public
     */
    public function port($port)
    {
        $this->setPort($port);
        return $this;
    }

    /**
     * Getter for port.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string|null The port if set, otherwise NULL.
     * @access public
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sends a command to a Memcached instance and returns the parsed result.
     *
     * @param string $command The command to send to Memcached daemon
     * @param string $data    The data to pass with command
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The result from Memcached daemon
     * @access public
     * @throws Memcached_Exception
     */
    public function send($command, $data = '')
    {
        // Check if command is allowed
        if (in_array($command, $this->allowedCommands) === false) {
            throw new Memcached_Exception(
                sprintf('The command "%s" is not allowed!', $command)
            );
        }

        // Get socket
        $socket = $this->connect($this->getHost(), $this->getPort());

        // The buffer to be filled with response
        $buffer = '';

        // Check for valid resource ...
        if (is_resource($socket) === false) {
            throw new Memcached_Exception(
                sprintf('Can\'t connect to %s:%s', $this->getHost(), $this->getPort())
            );
        }

        // Dispatch command in some different ways ... depending on command ...
        fwrite($socket, $data);

        // Fetch while receiving data ...
        while ((!feof($socket))) {
            // Fetch Bytes from socket ...
            $buffer .= fgets($socket, self::SOCKET_READ_FETCH_BYTES);

            // Response max. 64 Bit value = 8 Bytes - We reed 256 Bytes at once so one round is definitive enough ;)
            if ($command === self::COMMAND_INCR || $command === self::COMMAND_DECR) {
                break;
            }

            foreach ($this->sigsEnd as $sigEnd) {
                if (preg_match('/(' . $sigEnd . '.*\R)/imu', $buffer)) {
                    break 2;
                }
            }
        }

        // Check for HARD errors. Not an unsuccessful response from command -> here = real errors
        if (preg_match('/' . self::ERROR . '(.*)\R/mu', $buffer, $error) > 0) {
            // ERROR\r\n
            $this->lastResult(self::RESULT_FAILURE);

        } elseif (preg_match('/' . self::ERROR_CLIENT . '(.*)\R/mu', $buffer, $error) > 0) {
            // CLIENT_ERROR\r\n
            $this->lastResult(self::RES_CLIENT_ERROR);

        } elseif (preg_match('/' . self::ERROR_SERVER . '(.*)\R/mu', $buffer, $error) > 0) {
            // SERVER_ERROR\r\n
            $this->lastResult(self::RES_SERVER_ERROR);
        }

        // Check for
        if ($this->getLastResult() !== 0) {
            throw new Memcached_Exception(
                sprintf(
                    'Error while issuing command "%s" on host "%s"', $command, $this->getHost() . ':' . $this->getPort()
                )
            );
        }

        // Parse the response and return result ...
        return $this->parseResponse($command, $buffer);
    }

    /**
     * Touch an existing key by offset.
     *
     * @param string $key        The key to touch
     * @param int    $expiration When to expire
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The result of operation
     * @access public
     */
    public function touch($key, $expiration)
    {
        /**
         * touch <key> <exptime> [noreply]\r\n
         */

        // Build packet to send ...
        $data = self::COMMAND_TOUCH . self::COMMAND_SEPARATOR   .
            $key                    . self::COMMAND_SEPARATOR   .
            $expiration             . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_TOUCH, $data);
    }

    /**
     * Increments an existing key by offset.
     * Does currently not support the creation of not existing keys.
     *
     * @param string $key          The key to increment
     * @param int    $offset       How much to increment
     * @param int    $initialValue Which value to set for not existing keys
     * @param int    $expiration   When to expire
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The result of operation
     * @access public
     */
    public function increment($key, $offset = 1, $initialValue = 0, $expiration = 0)
    {
        /**
         * incr <key> <value> [noreply]\r\n
         */

        // Build packet to send ...
        $data = self::COMMAND_INCR . self::COMMAND_SEPARATOR   .
            $key                   . self::COMMAND_SEPARATOR   .
            $offset                . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_INCR, $data);
    }

    /**
     * Proxy to increment().
     *
     * Increments an existing key by offset.
     * Does currently not support the creation of not existing keys.
     *
     * @param string $key          The key to increment
     * @param int    $offset       How much to increment
     * @param int    $initialValue Which value to set for not existing keys
     * @param int    $expiration   When to expire
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The result of operation
     * @access public
     */
    public function incr($key, $offset = 1, $initialValue = 0, $expiration = 0)
    {
        return $this->increment($key, $offset, $initialValue, $expiration);
    }

    /**
     * Decrements an existing key by offset.
     * Does currently not support the creation of not existing keys.
     *
     * @param string $key          The key to decrement
     * @param int    $offset       How much to decrement
     * @param int    $initialValue Which value to set for not existing keys
     * @param int    $expiration   When to expire
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The result of operation
     * @access public
     */
    public function decrement($key, $offset = 1, $initialValue = 0, $expiration = 0)
    {
        /**
         * decr <key> <value> [noreply]\r\n
         */

        // Build packet to send ...
        $data = self::COMMAND_DECR . self::COMMAND_SEPARATOR   .
            $key                   . self::COMMAND_SEPARATOR   .
            $offset                . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_DECR, $data);
    }

    /**
     * Sets a key with value and the passed metadata on Memcached instance.
     * "set" means "store this data".
     *
     * @param string   $key        The key to set
     * @param mixed    $value      The value to set
     * @param int      $expiration The expire time as seconds from now (must be < 30 days) OR a timestamp
     * @param int      $flags      The flags to set
     * @param int|null $bytes      The length in bytes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command set
     * @access public
     */
    public function set($key, $value, $expiration = 0, $flags = 0, $bytes = null)
    {
        /**
         * set <key> <flags> <exptime> <bytes> [noreply]\r\n
         * <value>\r\n
         */

        // Bit 1 = serialize (e.g. Classes or Arrays, Strings ...)
        if (
            $this->isSerializable($value)    === true &&
            $this->bitmask($flags, array(1)) === true
        ) {
            // Activate default bit to detect serialization
            $flags &= self::DEFAULT_BITMASK;
            $value  = serialize($value);
            $bytes  = null;

        } else {
            // Real numbers should keep real numbers
            // Bit 2 = int , 3 = double/float
            if (is_double($value) === true) {
                $flags |= 4;

            } elseif (is_int($value) === true) {
                $flags |= 2;
            }
        }

        // Calculate bytes if not precalculated
        $bytes = ($bytes !== null) ? $bytes : strlen($value);

        // Build packet to send ...
        $data = self::COMMAND_SET . self::COMMAND_SEPARATOR   .
            $key                  . self::COMMAND_SEPARATOR   .
            $flags                . self::COMMAND_SEPARATOR   .
            $expiration           . self::COMMAND_SEPARATOR   .
            $bytes                . self::COMMAND_TERMINATOR  .
            $value                . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_SET, $data);
    }

    /**
     * Returns TRUE if value can be serialized, otherwise FALSE.
     *
     * We do not convert numbers cause memcached cant increase them otherwise!
     *
     * @param mixed $value The value to check
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if is serializable, otherwise FALSE
     * @access public
     */
    protected function isSerializable($value)
    {
        $type = gettype($value);

        return (
            $type !== "float"   &&
            $type !== "double"  &&
            $type !== "integer"
        );
    }

    /**
     * Adds a key with value and the passed metadata on Memcached instance.
     * "add" means "store this data, but only if the server *doesn't* already
     * hold data for this key".
     *
     * @param string   $key        The key to set
     * @param mixed    $value      The value to set
     * @param int      $expiration The expire time as seconds from now (must be < 30 days) OR a timestamp
     * @param int      $flags      The flags to set
     * @param int|null $bytes      The length in bytes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command add
     * @access public
     */
    public function add($key, $value, $expiration = 0, $flags = 0, $bytes = null)
    {
        /**
         * add <key> <flags> <exptime> <bytes> [noreply]\r\n
         * <value>\r\n
         */

        // Calculate bytes if not precalculated
        $bytes = ($bytes !== null) ? $bytes : strlen($value);

        // Build packet to send ...
        $data = self::COMMAND_ADD . self::COMMAND_SEPARATOR   .
            $key                  . self::COMMAND_SEPARATOR   .
            $flags                . self::COMMAND_SEPARATOR   .
            $expiration           . self::COMMAND_SEPARATOR   .
            $bytes                . self::COMMAND_TERMINATOR  .
            $value                . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_ADD, $data);
    }

    /**
     * Replaces a key with value and the passed metadata on Memcached instance.
     * "replace" means "store this data, but only if the server *does*
     * already hold data for this key".
     *
     * @param string   $key        The key to set
     * @param mixed    $value      The value to set
     * @param int      $expiration The expire time as seconds from now (must be < 30 days) OR a timestamp
     * @param int      $flags      The flags to set
     * @param int|null $bytes      The length in bytes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command replace
     * @access public
     */
    public function replace($key, $value, $expiration = 0, $flags = 0, $bytes = null)
    {
        /**
         * replace <key> <flags> <exptime> <bytes> [noreply]\r\n
         * <value>\r\n
         */

        // Calculate bytes if not precalculated
        $bytes = ($bytes !== null) ? $bytes : strlen($value);

        // Build packet to send ...
        $data = self::COMMAND_REPLACE . self::COMMAND_SEPARATOR   .
            $key                      . self::COMMAND_SEPARATOR   .
            $flags                    . self::COMMAND_SEPARATOR   .
            $expiration               . self::COMMAND_SEPARATOR   .
            $bytes                    . self::COMMAND_TERMINATOR  .
            $value                    . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_REPLACE, $data);
    }

    /**
     * Appends a key with value and the passed metadata on Memcached instance.
     * "append" means "add this data to an existing key after existing data".
     *
     * @param string   $key        The key to set
     * @param mixed    $value      The value to set
     * @param int      $expiration The expire time as seconds from now (must be < 30 days) OR a timestamp
     * @param int      $flags      The flags to set
     * @param int|null $bytes      The length in bytes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command append
     * @access public
     */
    public function append($key, $value, $expiration = 0, $flags = 0, $bytes = null)
    {
        /**
         * replace <key> <flags> <exptime> <bytes> [noreply]\r\n
         * <value>\r\n
         */

        // Calculate bytes if not precalculated
        $bytes = ($bytes !== null) ? $bytes : strlen($value);

        // Build packet to send ...
        $data = self::COMMAND_APPEND . self::COMMAND_SEPARATOR   .
            $key                     . self::COMMAND_SEPARATOR   .
            $flags                   . self::COMMAND_SEPARATOR   .
            $expiration              . self::COMMAND_SEPARATOR   .
            $bytes                   . self::COMMAND_TERMINATOR  .
            $value                   . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_APPEND, $data);
    }

    /**
     * Prepends data to an existing key before existing data.
     * "prepend" means "add this data to an existing key before existing data".
     *
     * @param string   $key        The key to set
     * @param mixed    $value      The value to set
     * @param int      $expiration The expire time as seconds from now (must be < 30 days) OR a timestamp
     * @param int      $flags      The flags to set
     * @param int|null $bytes      The length in bytes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command prepend
     * @access public
     */
    public function prepend($key, $value, $expiration = 0, $flags = 0, $bytes = null)
    {
        /**
         * replace <key> <flags> <exptime> <bytes> [noreply]\r\n
         * <value>\r\n
         */

        // Calculate bytes if not precalculated
        $bytes = ($bytes !== null) ? $bytes : strlen($value);

        // Build packet to send ...
        $data = self::COMMAND_PREPEND . self::COMMAND_SEPARATOR   .
            $key                      . self::COMMAND_SEPARATOR   .
            $flags                    . self::COMMAND_SEPARATOR   .
            $expiration               . self::COMMAND_SEPARATOR   .
            $bytes                    . self::COMMAND_TERMINATOR  .
            $value                    . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_PREPEND, $data);
    }

    /**
     * Prepends data to an existing key before existing data.
     * "prepend" means "add this data to an existing key before existing data".
     *
     * @param string   $token      a unique 64-bit value of an existing entry.
     *                             Clients should use the value returned from the
     *                             "gets" command when issuing "cas" updates.
     * @param string   $key        The key to set
     * @param mixed    $value      The value to set
     * @param int      $expiration The expire time as seconds from now (must be < 30 days) OR a timestamp
     * @param int      $flags      The flags to set
     * @param int|null $bytes      The length in bytes
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command cas
     * @access public
     */
    public function cas($token, $key, $value, $expiration = 0, $flags = 0, $bytes = null)
    {
        /**
         * cas <key> <flags> <exptime> <bytes> <cas unique> [noreply]\r\n
         * <value>\r\n
         */

        // Calculate bytes if not precalculated
        $bytes = ($bytes !== null) ? $bytes : strlen($value);

        // Build packet to send ...
        $data = self::COMMAND_CAS . self::COMMAND_SEPARATOR   .
            $key                  . self::COMMAND_SEPARATOR   .
            $flags                . self::COMMAND_SEPARATOR   .
            $expiration           . self::COMMAND_SEPARATOR   .
            $bytes                . self::COMMAND_SEPARATOR   .
            $token                . self::COMMAND_TERMINATOR  .
            $value                . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_CAS, $data);
    }

    /**
     * Returns the response for passed key.
     *
     * @param string $key      The key to return response for.
     * @param bool   $metadata TRUE to return metadata like lifetime ... as well, FALSE to return value only
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command get
     * @access public
     */
    public function get($key, $metadata = false)
    {
        /**
         * get <key>*\r\n
         */

        // Build packet to send ...
        $data = self::COMMAND_GET . self::COMMAND_SEPARATOR   .
            $key                  . self::COMMAND_TERMINATOR;

        $result = $this->send(self::COMMAND_GET, $data);

        // Strip all overhead if no metadata requested!
        if ($metadata === false) {
            $result = array_column($result, 'value', 0);
            $result = $result[0];
        }

        return $result;
    }

    /**
     * Returns the response for passed keys.
     *
     * @param array $keys     The keys to return response for.
     * @param bool  $metadata TRUE to return metadata like lifetime ... as well, FALSE to return value only
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command get
     * @access public
     */
    public function gets(array $keys, $metadata = false)
    {
        /**
         * gets <key>*\r\n
         */

        // Build key request string
        $keys = implode(self::COMMAND_SEPARATOR, $keys);

        // Build packet to send ...
        $data = self::COMMAND_GETS . self::COMMAND_SEPARATOR   .
            $keys                  . self::COMMAND_TERMINATOR;

        $result = $this->send(self::COMMAND_GETS, $data);

        // Strip all overhead if no metadata requested!
        if ($metadata === false) {
            $result = array_column($result, 'value', 'key');
        }

        return $result;
    }

    /**
     * Deletes an element/key (+ its data) from Memcached instance.
     *
     * @param string $key The key to delete.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command delete
     * @access public
     */
    public function delete($key)
    {
        /**
         * delete <key> [noreply]\r\n
         */

        // Build packet to send ...
        $data = self::COMMAND_DELETE . self::COMMAND_SEPARATOR  .
            $key                     . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_DELETE, $data);
    }

    /**
     * Stats - sends the stats command with default or custom type to Memcached instance.
     *
     * @param string $type      The type to send to Memcached instance.
     * @param string $argument1 Optional additional argument.
     * @param string $argument2 Optional additional argument.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command stats
     * @access public
     */
    public function stats($type = '', $argument1 = '', $argument2 = '')
    {
        /**
         * stats <type> [noreply]\r\n
         * [TYPES <reset, malloc, maps, cachedump, slabs, items, sizes>]
         */

        if ($type !== '') {
            $type = self::COMMAND_SEPARATOR . $type;
        }

        if ($argument1 !== '') {
            $argument1 = self::COMMAND_SEPARATOR . $argument1;
        }

        if ($argument2 !== '') {
            $argument2 = self::COMMAND_SEPARATOR . $argument2;
        }

        // Build packet to send ...
        $data = self::COMMAND_STATS . $type .
            $argument1 .
            $argument2 .
            self::COMMAND_TERMINATOR;

        $result = $this->send(self::COMMAND_STATS, $data);

        // Slabs stats delivers data twice?! and needs some special handling
        if ($type === self::COMMAND_SEPARATOR . self::STATS_TYPE_SLABS) {
            // Every call after slabs returns memory data? ... so we use it here ...
            $memory = $this->send(self::COMMAND_STATS, $data);

            foreach ($memory as $host => $slabs) {
                foreach ($slabs as $key => $value) {
                    if (is_numeric($key) === true) {
                        $result[$host][$key] = array_merge($result[$host][$key], $value);
                    } else {
                        $result[$host][$key] = $value;
                    }
                }
            }

            $uuid = md5($this->getHost() . ':' . $this->getPort());
            fclose(self::$connections[$this->getPersistentId()][$uuid]);
            unset(self::$connections[$this->getPersistentId()][$uuid]);
        }

        return $result;
    }

    /**
     * Version - sends the version command to Memcached instance and returns the result.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return mixed The response for command version
     * @access public
     */
    public function version()
    {
        /**
         * version\r\n
         */

        // Build packet to send ...
        $data = self::COMMAND_VERSION . self::COMMAND_TERMINATOR;

        return $this->send(self::COMMAND_VERSION, $data);
    }

    /*------------------------------------------------------------------------------------------------------------------
    | Internal Setter + Getter (protected)
    +-----------------------------------------------------------------------------------------------------------------*/

    /**
     * Setter for persistent Id.
     *
     * @param string $persistentId The persistent Id to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setPersistentId($persistentId)
    {
        $this->persistentId = $persistentId;
    }

    /**
     * Setter for persistent Id.
     *
     * @param string $persistentId The persistent Id to store
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function persistentId($persistentId)
    {
        $this->setPersistentId($persistentId);
        return $this;
    }

    /**
     * Getter for persistent Id.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The persistent Id set at instantiation or generated by this instance
     * @access protected
     */
    protected function getPersistentId()
    {
        return $this->persistentId;
    }

    /**
     * Setter for lastResult.
     *
     * @param int $response The response
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return void
     * @access protected
     */
    protected function setLastResult($response)
    {
        $this->lastResult = $response;
    }

    /**
     * Setter for lastResult.
     *
     * @param int $response The response array (command => buffer)
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return $this Instance for chaining
     * @access protected
     */
    protected function lastResult($response)
    {
        $this->setLastResult($response);
        return $this;
    }

    /**
     * Getter for lastResult.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int The last response
     * @access protected
     */
    protected function getLastResult()
    {
        return $this->lastResult;
    }

    /**
     * Simple generic hashing of dynamic input.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The calculated UUID
     * @access protected
     */
    protected function uuid()
    {
        return sha1(implode('.', func_get_args()));
    }

    /**
     * Parses a response from a Memcached daemon
     *
     * @param string $command The command which has triggered the buffer response from instance.
     * @param string $buffer  The buffer to parse.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool|mixed FALSE on error, otherwise parsed response
     * @access protected
     * @throws Memcached_Exception
     */
    protected function parseResponse($command, $buffer)
    {
        // At this point we retrieve a raw response containing at least a trailing terminator - rip it
        $response = substr($buffer, 0, strlen($buffer) - strlen(self::COMMAND_TERMINATOR));

        // The result
        $result = array();

        // Helper required for parsing ...
        $lines = explode(self::COMMAND_TERMINATOR, $response);

            // PARSER for <get> <gets>
        if (
            $command === self::COMMAND_GET ||
            $command === self::COMMAND_GETS
        ) {
            // Iterator for lines ...
            $line  = 0;

            // Loop as long as line is !== END\r\n Terminator (1 line META 1 line DATA)
            while ($lines[$line] !== self::RESPONSE_END) {
                /**
                 * Try to fetch metadata. Why try? Cause we can receive multiple lines for a value. If the current key
                 * reference to a simple value like an integer (65000 for example) then we have one line <meta> data and
                 * one line <value> data. But if the value contains a "\r\n" itself it breaks this simple assumption so
                 * that we must
                 */
                $metaData = explode(self::COMMAND_SEPARATOR, $lines[$line]);

                // Ensure we did not receive trash
                if ($metaData[0] !== self::RESPONSE_VALUE) {
                    throw new Memcached_Exception(
                        sprint('Awaited "%s" but received "%s"', self::RESPONSE_VALUE, $metaData[0])
                    );
                }

                // Value must be at least starting on next line - and can continue to spawn on n following lines ...
                $key    = $metaData[1];
                $value  = '';
                $flags  = $metaData[2];
                $length = $metaData[3];
                $cas    = (isset($metaData[4])) ? $metaData[4] : null;
                $frame  = 0;

                // Fetch whole & complete value!
                while (strlen($value) < $length) {
                    ++$frame;
                    $value .= $lines[$line + $frame];
                }

                // Ensure that we are able to return exactly the same types as stored ...
                $result[$key] = array(
                    // 1st bit set = we use un-/serialize to keep the values intact ...
                    'value' => $value,
                    'key'   => $key,
                    'meta'  => array(
                        'key'    => $key,
                        'flags'  => $flags,
                        'length' => $length,
                        'cas'    => $cas,
                        'frames' => $frame
                    )
                );

                // Check for value converting and length fixing
                if ($this->bitmask($flags, array(self::DEFAULT_BITMASK)) === true) {
                    $value = unserialize($value);
                    $length = strlen($value);

                } elseif ($this->bitmask($flags, array(2)) === true) {
                    $value = intval($value);
                    $length = strlen($value);

                } elseif ($this->bitmask($flags, array(4)) === true) {
                    $value  = doubleval($value);
                    $length = strlen($value);
                }

                $result[$key]['value']          = $value;
                $result[$key]['meta']['length'] = $length;

                // Increment by one and check
                $line += 1 + $frame;
            }

            // Inject finally the global metadata from response ...
            $result['meta'] = $buffer;

            // Memcached compatible success
            $this->lastResult(self::RESULT_SUCCESS);

            // PARSER for <set> <add> <replace> <append> <prepend> <cas>
        } elseif (
            $command === self::COMMAND_SET     ||
            $command === self::COMMAND_ADD     ||
            $command === self::COMMAND_REPLACE ||
            $command === self::COMMAND_APPEND  ||
            $command === self::COMMAND_PREPEND ||
            $command === self::COMMAND_CAS
        ) {
            /**
             * "STORED\r\n", to indicate success.
             *
             * "NOT_STORED\r\n" to indicate the data was not stored, but not because of an error. This normally means
             * that the condition for an "add" or a "replace" command was not met.
             *
             * "EXISTS\r\n" to indicate that the item you are trying to store with a "cas" command has been modified
             * since you last fetched it.
             *
             * "NOT_FOUND\r\n" to indicate that the item you are trying to store with a "cas" command did not exist.
             */
            $result = ($buffer === self::RESPONSE_STORED . self::COMMAND_TERMINATOR);

            if ($result === true) {
                // success
                $this->lastResult(self::RESULT_SUCCESS);

            } else {
                // Set detailed error in error case
                if (
                    $lines[0] === self::RESPONSE_NOT_STORED
                ) {
                    $this->lastResult(self::RESULT_NOTSTORED);
                } elseif (
                    $lines[0] === self::RESPONSE_EXISTS
                ) {
                    $this->lastResult(self::RESULT_DATA_EXISTS);
                } elseif (
                    $lines[0] === self::RESPONSE_NOT_FOUND
                ) {
                    $this->lastResult(self::RESULT_NOTFOUND);
                } else {
                    $this->lastResult(self::RESULT_FAILURE);
                }
            }

            // PARSER for <delete>
        } elseif ($command === self::COMMAND_DELETE) {
            /**
             * SUCCESS = RESPONSE = "DELETED"
             * FAILED  = RESPONSE = "NOT_FOUND"
             */
            $metaData = explode(self::COMMAND_SEPARATOR, $lines[0]);

            $result = ($metaData[0] === self::RESPONSE_DELETED);

        } elseif (
            // PARSER for <stats>*
            $command === self::COMMAND_STATS
        ) {
            // Iterator for lines ...
            $line = 0;

            if ($lines[count($lines) - 1] !== self::RESPONSE_END) {
                $lines[] = self::RESPONSE_END;
            }

            // Loop as long as line is !== END\r\n Terminator (1 line META 1 line DATA)
            while ($lines[$line] !== self::RESPONSE_END) {
                /**
                 * Try to fetch in this way: split descriptor/key from value - each stats entry is on one line
                 * STAT <key> <value>\r\n
                 */
                $metaData = explode(self::COMMAND_SEPARATOR, $lines[$line]);

                // Ensure we did not receive trash
                if ($metaData[0] !== self::RESPONSE_STAT && $metaData[0] !== self::RESPONSE_ITEM) {
                    throw new Memcached_Exception(
                        sprintf('Awaited "%s" but received "%s"', self::RESPONSE_STAT, $metaData[0])
                    );
                }

                $nodes      = explode(':', $metaData[1]);
                $countNodes = count($nodes);

                if ($countNodes === 2) {
                    // Slab set?
                    if (isset($result[$nodes[0]]) === false) {
                        $result[$nodes[0]] = array();
                    }

                    $result[$nodes[0]][$nodes[1]] = $metaData[2];

                } elseif ($countNodes === 3) {
                    // ???
                    if (isset($result[$nodes[0]]) === false) {
                        $result[$nodes[0]] = array();

                        if (isset($result[$nodes[0]][$nodes[1]]) === false) {
                            $result[$nodes[0]][$nodes[1]] = array();
                        }

                    }

                    $result[$nodes[0]][$nodes[1]][$nodes[2]] = $metaData[2];

                } else {
                    //
                    $identifier = array_shift($metaData);
                    $key        = array_shift($metaData);
                    $value      = implode(self::COMMAND_SEPARATOR, $metaData);

                    $result[$key] = $value;
                };

                // Next line
                ++$line;
            }

            $result = array(
                $this->getHost() . ':' . $this->getPort() => $result
            );

        } elseif ($command === self::COMMAND_VERSION) {
            /**
             * SUCCESS = RESPONSE = "\r\n"
             * FAILED  = RESPONSE = "???"
             */
            $metaData = explode(self::COMMAND_SEPARATOR, $lines[0]);

            if ($metaData[0] === strtoupper(self::COMMAND_VERSION)) {
                $result = $metaData[1];
            } else {
                $result = false;
            }

        } elseif ($command === self::COMMAND_INCR || $command === self::COMMAND_DECR) {
            /**
             * SUCCESS = RESPONSE = "NEW VALUE AFTER OPERATION [2 will incr to X] OR [1 will decr to Y] ..."
             * FAILED  = RESPONSE = "NOT_FOUND"
             */
            $metaData = explode(self::COMMAND_SEPARATOR, $lines[0]);

            if ($buffer === self::RESPONSE_NOT_FOUND . self::COMMAND_TERMINATOR) {
                $result = false;

            } else {
                // Insert the response (= new value) as result
                $result = (double)$metaData[0];
            }

        } else {
            echo 'Babäm!';
            echo '<pre>';
            var_dump($buffer);
            echo '</pre>';
            die;
        }

        return $result;
    }

    /**
     * Parser for all read operations:
     * <>,
     *
     *
     */
    protected function parseReadResponse()
    {

    }

    protected function parseWriteResponse()
    {

    }

    protected function parseStatisticResponse()
    {

    }

    /**
     * Checks the bit positions of bits in value and return bool result.
     *
     * @param int   $value The value to check
     * @param array $bits  The bits to check in value
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return bool TRUE if all bits from $bits set, otherwise FALSE
     * @access protected
     */
    protected function bitmask($value, array $bits)
    {
        // Assume true and the first false will return false and break
        $result = true;

        foreach ($bits as $bit) {
            $result = $result && ($value & $bit);
            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * Formats a response for a "get" request accordingly to responded data.
     *
     * @param null  $value The value returned for a key (NULL = invalid key)
     * @param int   $flags The flags for the stored data
     * @param int   $bytes The length of the stored data in bytes
     * @param array $merge An optional array to merge with response
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The combined values as an array
     * @access protected
     */
    protected function formatValueResponse(
              $value = null,
              $flags = 0,
              $bytes = 0,
        array $merge = null
    ) {
        $response = array(
            'value' => $value,
            'flags' => $flags,
            'bytes' => $bytes,
        );

        if ($merge !== null) {
            $response = array_merge($response, $merge);
        }

        return $response;
    }

    /**
     * Formats a response for a "stats" request accordingly to responded data.
     *
     * @param null  $value The value returned for a key (NULL = invalid key)
     * @param int   $bytes The length of the stored data in bytes
     * @param array $merge An optional array to merge with response
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The combined values as an array
     * @access protected
     */
    protected function formatStatsResponse(
              $value = null,
              $bytes = 0,
        array $merge = null
    ) {
        $response = array(
            'value' => $value,
            'bytes' => $bytes,
        );

        if ($merge !== null) {
            $response = array_merge($response, $merge);
        }

        return $response;
    }

    /**
     * Connects to a Memcached host.
     *
     * @param string $host The host to connect to
     * @param int    $port The port the Memcached daemon is listening on
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return resource|null The resource (socket) on success, otherwise NULL
     * @access protected
     * @throws Memcached_Exception
     */
    protected function connect($host, $port)
    {
        $uuid = md5($host . ':' . $port);

        if (isset(self::$connections[$this->getPersistentId()][$uuid]) === false) {
            try {
                self::$connections[$this->getPersistentId()][$uuid] = @fsockopen($host, $port);

            } catch (Exception $e) {
                throw new Memcached_Exception(
                    sprintf('Error while connecting to Memcached on host: "%s:%s" (%s)', $host, $port, $uuid)
                );
            }
        }

        return self::$connections[$this->getPersistentId()][$uuid];
    }
}

