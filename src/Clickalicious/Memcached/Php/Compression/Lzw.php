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

use Clickalicious\Memcached\Php\Exception;

/**
 * Class Lzw
 *
 * Lzw.php - LZW-compression (LZW = Lempel–Ziv–Welch).
 * http://en.wikipedia.org/wiki/Lempel%E2%80%93Ziv%E2%80%93Welch
 *
 * LZW-compression implementation in plain vanilla PHP.
 *
 * Originally from: https://code.google.com/p/smt2/
 *
 * @package Clickalicious\Memcached\Php\Compression
 * @author  Benjamin Carl <opensource@clickalicious.de>
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
        $countData  = count($data);

        for ($i = 1; $i < $countData; ++$i) {
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

        $out[]    = strlen($phrase) > 1 ? $dictionary[$phrase] : $this->charCodeAt($phrase,0);
        $countOut = count($out);

        for ($i = 0; $i < $countOut; ++$i) {
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
        $data             = $this->multibyteStringSplit($buffer);

        $currentCharacter = $data[0];
        $oldPhrase        = $currentCharacter;
        $out              = array($currentCharacter);
        $code             = 256;
        $countData        = count($data);

        for ($i = 1; $i < $countData; ++$i) {
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
    protected function multibyteStringSplit($string)
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
     * @return string The unicode character
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
