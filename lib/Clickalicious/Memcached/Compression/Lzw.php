<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Clickalicious\Memcached\Compression;

/**
 * Memcached.php
 *
 * Lzw.php - LZW-compression (LZW = Lempel–Ziv–Welch).
 * http://en.wikipedia.org/wiki/Lempel%E2%80%93Ziv%E2%80%93Welch
 *
 * LZW-compression implementation in plain vanilla PHP.
 *
 * Originally from: https://code.google.com/p/smt2/
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
 * @subpackage Clickalicious_Memcached_Compression
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 * @see        http://en.wikipedia.org/wiki/Lempel%E2%80%93Ziv%E2%80%93Welch
 */

require_once 'CompressionInterface.php';

use Clickalicious\Memcached\Exception;

/**
 * Memcached.php
 *
 * LZW-compression (LZW = Lempel–Ziv–Welch).
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_Compression
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 * @see        http://en.wikipedia.org/wiki/Lempel%E2%80%93Ziv%E2%80%93Welch
 */
class Lzw implements CompressionInterface
{
    /**
     * Constructor.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return Lzw
     * @access public
     * @throws Exception
     */
    public function __construct()
    {
        if (!extension_loaded('mbstring')) {
            throw new Exception(
                'Multibyte String extension is required for LZW compression.'
            );
        }
    }

    /**
     * Compresses a buffer.
     *
     * @param string $buffer The buffer to compress
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The compressed input
     * @access protected
     */
    public function compress($buffer)
    {
        $dictionary = array();
        $data       = str_split($buffer . '');
        $out        = array();
        $phrase     = $data[0];
        $code       = 256;

        for ($i = 1; $i < count($data); ++$i) {
            $currentCharacter = $data[$i];

            if (isset($dictionary[$phrase.$currentCharacter])) {
                $phrase .= $currentCharacter;

            } else {
                $out[] = strlen($phrase) > 1 ? $dictionary[$phrase] : $this->charCodeAt($phrase,0);
                $dictionary[$phrase.$currentCharacter] = $code;
                $code++;
                $phrase = $currentCharacter;
            }
        }

        $out[] = strlen($phrase) > 1 ? $dictionary[$phrase] : $this->charCodeAt($phrase,0);

        for ($i = 0; $i < count($out); ++$i) {
            $out[$i] = $this->unichr($out[$i]);
        }

        return implode('', $out);
    }

    /**
     * Decompresses a buffer.
     *
     * @param string $buffer The buffer to decompress
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return string The decompressed input
     * @access protected
     */
    public function decompress($buffer)
    {
        $dictionary       = array();
        $data             = $this->mb_strsplit($buffer);

        $currentCharacter = $data[0];
        $oldPhrase        = $currentCharacter;
        $out              = array($currentCharacter);
        $code             = 256;

        for ($i = 1; $i < count($data); ++$i) {
            $currCode = $this->uniord($data[$i]);

            if ($currCode < 256) {
                $phrase = $data[$i];

            } else {
                $phrase = isset($dictionary[$currCode]) ? $dictionary[$currCode] : $oldPhrase.$currentCharacter;
            }

            $out[]             = $phrase;
            $currentCharacter  = $phrase[0];
            $dictionary[$code] = $oldPhrase . $currentCharacter;

            $code++;

            $oldPhrase = $phrase;
        }

        return implode('', $out);
    }

    /**
     * Multi-Byte implementation of str_split().
     *
     * @param string $string The input to split
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The splitted string
     * @access protected
     */
    protected function mb_strsplit($string)
    {
        return preg_split('/(?<!^)(?!$)/u', $string);
    }

    /**
     * Returns the character from a position in buffer.
     *
     * @param string $buffer   The input to return character from
     * @param int    $position The position to return character from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int The position
     * @access protected
     */
    protected function charCodeAt($buffer, $position)
    {
        return $this->uniord($buffer[$position]);
    }

    /**
     * Returns a one-character string containing the character specified by ascii.
     * Unicode implementation.
     *
     * @param string $character The character
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The unicode character
     * @access protected
     */
    protected function unichr($character)
    {
        return mb_convert_encoding(
            pack('n', $character), 'UTF-8', 'UTF-16BE'
        );
    }

    /**
     * Returns the ASCII value of the first character of string.
     * Unicode implementation.
     *
     * @param string $buffer The buffer to return character from
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return int The ASCII value of the character
     * @access protected
     */
    protected function uniord($buffer)
    {
        list(, $ord) = unpack(
            'N',
            mb_convert_encoding($buffer, 'UCS-4BE', 'UTF-8')
        );

        return $ord;
    }
}
