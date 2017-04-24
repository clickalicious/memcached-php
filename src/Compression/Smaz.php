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

namespace Clickalicious\Memcached\Php\Compression;

/**
 * Class Smaz
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
 * @package Clickalicious\Memcached\Php\Compression
 * @author  Benjamin Carl <opensource@clickalicious.de>
 */
class Smaz implements CompressionInterface
{
    /**
     * The encode book.
     *
     * @var array|null
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

                if($code !== null) {
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
        if (self::$encodeBook === null) {
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
