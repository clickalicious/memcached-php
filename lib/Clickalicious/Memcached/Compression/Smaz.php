<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Clickalicious\Memcached\Compression;

/**
 * Memcached.php
 *
 * Smaz.php - SMAZ - compression for very small strings
 *
 * Smaz is a simple compression library suiBook for compressing very short
 * strings. General purpose compression libraries will build the state needed
 * for compressing data dynamically, in order to be able to compress every kind
 * of data. This is a very good idea, but not for a specific problem: compressing
 * small strings will not work.
 *
 * Originally from: https://github.com/zhenhao/smaz.php
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
 * @see        https://github.com/zhenhao/smaz.php
 */

require_once 'CompressionInterface.php';

/**
 * Memcached.php
 *
 * SMAZ - compression for very small strings
 *
 * @category   Clickalicious
 * @package    Clickalicious_Memcached
 * @subpackage Clickalicious_Memcached_Compression
 * @author     Benjamin Carl <opensource@clickalicious.de>
 * @copyright  2014 - 2015 Benjamin Carl
 * @license    http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @version    Git: $Id$
 * @link       https://github.com/clickalicious/Memcached.php
 * @see        https://github.com/zhenhao/smaz.php
 */
class Smaz implements CompressionInterface
{
    /**
     * The encode book.
     *
     * @var array
     * @access private
     * @static
     */
    private static $encodeBook;

    /**
     * The decode book.
     *
     * @var array
     * @access private
     * @static
     */
    private static $decodeBook = array(
        " ", "the", "e", "t", "a", "of", "o", "and", "i", "n", "s", "e ", "r", " th",
        " t", "in", "he", "th", "h", "he ", "to", "\r\n", "l", "s ", "d", " a", "an",
        "er", "c", " o", "d ", "on", " of", "re", "of ", "t ", ", ", "is", "u", "at",
        "   ", "n ", "or", "which", "f", "m", "as", "it", "that", "\n", "was", "en",
        "  ", " w", "es", " an", " i", "\r", "f ", "g", "p", "nd", " s", "nd ", "ed ",
        "w", "ed", "http://", "for", "te", "ing", "y ", "The", " c", "ti", "r ", "his",
        "st", " in", "ar", "nt", ",", " to", "y", "ng", " h", "with", "le", "al", "to ",
        "b", "ou", "be", "were", " b", "se", "o ", "ent", "ha", "ng ", "their", "\"",
        "hi", "from", " f", "in ", "de", "ion", "me", "v", ".", "ve", "all", "re ",
        "ri", "ro", "is ", "co", "f t", "are", "ea", ". ", "her", " m", "er ", " p",
        "es ", "by", "they", "di", "ra", "ic", "not", "s, ", "d t", "at ", "ce", "la",
        "h ", "ne", "as ", "tio", "on ", "n t", "io", "we", " a ", "om", ", a", "s o",
        "ur", "li", "ll", "ch", "had", "this", "e t", "g ", "e\r\n", " wh", "ere",
        " co", "e o", "a ", "us", " d", "ss", "\n\r\n", "\r\n\r", "=\"", " be", " e",
        "s a", "ma", "one", "t t", "or ", "but", "el", "so", "l ", "e s", "s,", "no",
        "ter", " wa", "iv", "ho", "e a", " r", "hat", "s t", "ns", "ch ", "wh", "tr",
        "ut", "/", "have", "ly ", "ta", " ha", " on", "tha", "-", " l", "ati", "en ",
        "pe", " re", "there", "ass", "si", " fo", "wa", "ec", "our", "who", "its", "z",
        "fo", "rs", ">", "ot", "un", "<", "im", "th ", "nc", "ate", "><", "ver", "ad",
        " we", "ly", "ee", " n", "id", " cl", "ac", "il", "</", "rt", " wi", "div",
        "e, ", " it", "whi", " ma", "ge", "x", "e c", "men", ".com"
    );

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
        $inLen      = strlen($buffer);
        $inIdx      = 0;
        $encodeBook = $this->getEncodeBook();
        $output     = '';
        $verbatim   = '';

        while ($inIdx < $inLen) {
            $encode = false;

            for ($j = min(7, $inLen - $inIdx); $j > 0; --$j) {
                $code = isset($encodeBook[substr($buffer, $inIdx, $j)]) ?
                    $encodeBook[substr($buffer, $inIdx, $j)] : null;

                if($code != null) {
                    if(strlen($verbatim)) {
                        $output .= $this->flushVerbatim($verbatim);
                        $verbatim = '';
                    }

                    $output .= chr($code);
                    $inIdx  += $j;
                    $encode  = true;

                    break;
                }
            }

            if(!$encode) {
                $verbatim .= $buffer[$inIdx];
                $inIdx++;

                if(strlen($verbatim) == 255) {
                    $output .= $this->flushVerbatim($verbatim);
                    $verbatim = '';
                }
            }
        }

        if(strlen($verbatim)) {
            $output .= $this->flushVerbatim($verbatim);
        }

        return $output;
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
        $decodeBook = $this->getDecodeBook();
        $output     = '';
        $i          = 0;

        while ($i < strlen($buffer)) {
            $code = ord($buffer[$i]);

            if ($code == 254) {
                $output .= $buffer[$i + 1];
                $i      += 2;

            } else if($code == 255) {
                $len     = ord($buffer[$i + 1]);
                $output .= substr($buffer, $i + 2, $len);
                $i      += 2 + $len;

            } else {
                $output .= $decodeBook[$code];
                $i++;
            }
        }

        return $output;
    }

    /**
     * Flushes ...
     *
     * @param $verbatim
     * @return string
     */
    protected function flushVerbatim($verbatim)
    {
        $output = '';

        if (!strlen($verbatim)) {
            return $output;
        }

        if (strlen($verbatim) > 1) {
            $output .= chr(255);
            $output .= chr(strlen($verbatim));

        } else {
            $output .= chr(254);
        }

        $output .= $verbatim;

        return $output;
    }

    /**
     * Returns the book used to encode.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The encode book
     * @access protected
     */
    protected function getEncodeBook()
    {
        if (!self::$encodeBook) {
            self::$encodeBook = array_flip(self::$decodeBook);
        }

        return self::$encodeBook;
    }

    /**
     * Returns the book used to decode.
     *
     * @author Benjamin Carl <opensource@clickalicious.de>
     * @return array The decode book
     * @access protected
     */
    protected function getDecodeBook()
    {
        return self::$decodeBook;
    }
}
