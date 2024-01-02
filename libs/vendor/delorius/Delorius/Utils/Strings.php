<?php
namespace Delorius\Utils;

define('ICONV_IMPL_FIX', false);

use Delorius\Exception\Error;

/**
 * String tools library.
 */
class Strings
{

    /**
     * Checks if the string is valid for the specified encoding.
     * @param  string  byte stream to check
     * @param  string  expected encoding
     * @return bool
     */
    public static function checkEncoding($s, $encoding = 'UTF-8')
    {
        return $s === self::fixEncoding($s, $encoding);
    }

    /**
     * Returns correctly encoded string.
     * @param  string  byte stream to fix
     * @param  string  encoding
     * @return string
     */
    public static function fixEncoding($s, $encoding = 'UTF-8')
    {
        // removes xD800-xDFFF, xFEFF, xFFFF, x110000 and higher
        $s = @iconv('UTF-16', $encoding . '//IGNORE', iconv($encoding, 'UTF-16//IGNORE', $s)); // intentionally @
        return str_replace("\xEF\xBB\xBF", '', $s); // remove UTF-8 BOM
    }

    /**
     * Returns a specific character.
     * @param  int     codepoint
     * @param  string  encoding
     * @return string
     */
    public static function chr($code, $encoding = 'UTF-8')
    {
        return iconv('UTF-32BE', $encoding . '//IGNORE', pack('N', $code));
    }

    /**
     * Starts the $haystack string with the prefix $needle?
     * @param  string
     * @param  string
     * @return bool
     */
    public static function startsWith($haystack, $needle)
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }

    /**
     * Ends the $haystack string with the suffix $needle?
     * @param  string
     * @param  string
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Does $haystack contain $needle?
     * @param  string
     * @param  string
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        return strpos($haystack, $needle) !== FALSE;
    }

    /**
     * Returns a part of UTF-8 string.
     * @param  string
     * @param  int
     * @param  int
     * @return string
     */
    public static function substring($s, $start, $length = NULL)
    {
        if ($length === NULL) {
            $length = self::length($s);
        }
        return function_exists('mb_substr') ? mb_substr($s, $start, $length, 'UTF-8') : iconv_substr($s, $start, $length, 'UTF-8'); // MB is much faster
    }

    /**
     * Removes special controls characters and normalizes line endings and spaces.
     * @param  string  UTF-8 encoding or 8-bit
     * @return string
     */
    public static function normalize($s)
    {

        $s = self::normalizeNewLines($s);
        // remove control characters; leave \t + \n
        $s = preg_replace('#[\x00-\x08\x0B-\x1F\x7F]+#', '', $s);

        // right trim
        $s = preg_replace("#[\t ]+$#m", '', $s);

        // trailing spaces
        $s = trim($s, "\n");

        return $s;
    }

    /**
     * Standardize line endings to unix-like.
     * @param  string  UTF-8 encoding or 8-bit
     * @return string
     */
    public static function normalizeNewLines($s)
    {
        return str_replace(array("\r\n", "\r"), "\n", $s);
    }

    /**
     * Converts to ASCII.
     * @param  string  UTF-8 encoding
     * @return string  ASCII
     */
    public static function toAscii($s)
    {
        $s = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{10FFFF}]#u', '', $s);
        $s = strtr($s, '`\'"^~', "\x01\x02\x03\x04\x05");
        if (ICONV_IMPL_FIX) {
            if (ICONV_IMPL === 'glibc') {
                $s = @iconv('UTF-8', 'WINDOWS-1250//TRANSLIT', $s); // intentionally @
                $s = strtr($s, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"
                    . "\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"
                    . "\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"
                    . "\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe", "ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt");
            } else {
                $s = @iconv('UTF-8', 'ASCII//TRANSLIT', $s); // intentionally @
            }
        }
        $s = str_replace(array('`', "'", '"', '^', '~'), '', $s);
        return strtr($s, "\x01\x02\x03\x04\x05", '`\'"^~');
    }

    /**
     * Converts to web safe characters [a-z0-9-] text.
     * @param  string  UTF-8 encoding
     * @param  string  allowed characters
     * @param  bool
     * @return string
     */
    public static function webalize($s, $charlist = NULL, $lower = TRUE)
    {
        $s = self::toAscii($s);
        if ($lower) {
            $s = strtolower($s);
        }
        $s = trim(preg_replace('#[^a-z0-9_' . preg_quote($charlist, '#') . ']+#i', '-', $s), '-');
        return $s;
    }

    /**
     * Truncates string to maximal length.
     * @param  string  UTF-8 encoding
     * @param  int
     * @param  string  UTF-8 encoding
     * @return string
     */
    public static function truncate($s, $maxLen, $append = "\xE2\x80\xA6")
    {
        if (self::length($s) > $maxLen) {
            $maxLen = $maxLen - self::length($append);
            if ($maxLen < 1) {
                return $append;
            } elseif ($matches = self::match($s, '#^.{1,' . $maxLen . '}(?=[\s\x00-/:-@\[-`{-~])#us')) {
                return $matches[0] . $append;
            } else {
                return self::substring($s, 0, $maxLen) . $append;
            }
        }
        return $s;
    }

    /**
     * Indents the content from the left.
     * @param  string  UTF-8 encoding or 8-bit
     * @param  int
     * @param  string
     * @return string
     */
    public static function indent($s, $level = 1, $chars = "\t")
    {
        return $level < 1 ? $s : preg_replace('#(?:^|[\r\n]+)(?=[^\r\n])#', '$0' . str_repeat($chars, $level), $s, -1);
    }

    /**
     * Convert to lower case.
     * @param  string  UTF-8 encoding
     * @return string
     */
    public static function lower($s)
    {
        return mb_strtolower($s, 'UTF-8');
    }

    /**
     * Convert to upper case.
     * @param  string  UTF-8 encoding
     * @return string
     */
    public static function upper($s)
    {
        return mb_strtoupper($s, 'UTF-8');
    }

    /**
     * Convert first character to upper case.
     * @param  string  UTF-8 encoding
     * @return string
     */
    public static function firstUpper($s)
    {
        return self::upper(self::substring($s, 0, 1)) . self::substring($s, 1);
    }

    /**
     * Capitalize string.
     * @param  string  UTF-8 encoding
     * @return string
     */
    public static function capitalize($s)
    {
        return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Case-insensitive compares UTF-8 strings.
     * @param  string
     * @param  string
     * @param  int
     * @return bool
     */
    public static function compare($left, $right, $len = NULL)
    {
        if ($len < 0) {
            $left = self::substring($left, $len, -$len);
            $right = self::substring($right, $len, -$len);
        } elseif ($len !== NULL) {
            $left = self::substring($left, 0, $len);
            $right = self::substring($right, 0, $len);
        }
        return self::lower($left) === self::lower($right);
    }

    /**
     * Finds the length of common prefix of strings.
     * @param  string|array
     * @param  string
     * @return string
     */
    public static function findPrefix($strings, $second = NULL)
    {
        if (!is_array($strings)) {
            $strings = func_get_args();
        }
        $first = array_shift($strings);
        for ($i = 0; $i < strlen($first); $i++) {
            foreach ($strings as $s) {
                if (!isset($s[$i]) || $first[$i] !== $s[$i]) {
                    while ($i && $first[$i - 1] >= "\x80" && $first[$i] >= "\x80" && $first[$i] < "\xC0") {
                        $i--;
                    }
                    return substr($first, 0, $i);
                }
            }
        }
        return $first;
    }

    /**
     * Returns UTF-8 string length.
     * @param  string
     * @return int
     */
    public static function length($s)
    {
        return strlen(utf8_decode($s)); // fastest way
    }

    /**
     * Strips whitespace.
     * @param  string  UTF-8 encoding
     * @param  string
     * @return string
     */
    public static function trim($s, $charlist = " \t\n\r\0\x0B\xC2\xA0")
    {
        $charlist = preg_quote($charlist, '#');
        return preg_replace('#^[' . $charlist . ']+|[' . $charlist . ']+$#u', '', $s, -1);
    }

    /**
     * Pad a string to a certain length with another string.
     * @param  string  UTF-8 encoding
     * @param  int
     * @param  string
     * @return string
     */
    public static function padLeft($s, $length, $pad = ' ')
    {
        $length = max(0, $length - self::length($s));
        $padLen = self::length($pad);
        return str_repeat($pad, $length / $padLen) . self::substring($pad, 0, $length % $padLen) . $s;
    }

    /**
     * Pad a string to a certain length with another string.
     * @param  string  UTF-8 encoding
     * @param  int
     * @param  string
     * @return string
     */
    public static function padRight($s, $length, $pad = ' ')
    {
        $length = max(0, $length - self::length($s));
        $padLen = self::length($pad);
        return $s . str_repeat($pad, $length / $padLen) . self::substring($pad, 0, $length % $padLen);
    }

    /**
     * Reverse string.
     * @param  string  UTF-8 encoding
     * @return string
     */
    public static function reverse($s)
    {
        return @iconv('UTF-32LE', 'UTF-8', strrev(@iconv('UTF-8', 'UTF-32BE', $s)));
    }

    /**
     * Generate random string.
     * @param  int
     * @param  string
     * @return string
     */
    public static function random($length = 10, $charlist = '0-9a-z')
    {
        if ($length === 0) {
            return ''; // mcrypt_create_iv does not support zero length
        }

        $charlist = str_shuffle(preg_replace_callback('#.-.#', function ($m) {
            return implode('', range($m[0][0], $m[0][2]));
        }, $charlist));
        $chLen = strlen($charlist);

        $windows = defined('PHP_WINDOWS_VERSION_BUILD');
        if (function_exists('openssl_random_pseudo_bytes')
            && (PHP_VERSION_ID >= 50400 || !defined('PHP_WINDOWS_VERSION_BUILD')) // slow in PHP 5.3 & Windows
        ) {
            $rand3 = openssl_random_pseudo_bytes($length);
        }
        if (empty($rand3) && function_exists('mcrypt_create_iv') && (PHP_VERSION_ID >= 50307 || !$windows)) { // PHP bug #52523
            $rand3 = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        }
        if (empty($rand3) && !$windows && @is_readable('/dev/urandom')) {
            $rand3 = file_get_contents('/dev/urandom', FALSE, NULL, -1, $length);
        }
        if (empty($rand3)) {
            static $cache;
            $rand3 = $cache ?: $cache = md5(serialize($_SERVER), TRUE);
        }

        $s = '';
        for ($i = 0; $i < $length; $i++) {
            if ($i % 5 === 0) {
                list($rand, $rand2) = explode(' ', microtime());
                $rand += lcg_value();
            }
            $rand *= $chLen;
            $s .= $charlist[($rand + $rand2 + ord($rand3[$i % strlen($rand3)])) % $chLen];
            $rand -= (int)$rand;
        }
        return $s;
    }

    /**
     * Splits string by a regular expression.
     * @param  string
     * @param  string
     * @param  int
     * @return array
     */
    public static function split($subject, $pattern, $flags = 0)
    {
        $res = preg_split($pattern, $subject, -1, $flags | PREG_SPLIT_DELIM_CAPTURE);

        return $res;
    }

    /**
     * Performs a regular expression match.
     * @param  string
     * @param  string
     * @param  int  can be PREG_OFFSET_CAPTURE (returned in bytes)
     * @param  int  offset in bytes
     * @return mixed
     */
    public static function match($subject, $pattern, $flags = 0, $offset = 0)
    {
        if ($offset > strlen($subject))
            return NULL;

        $res = preg_match($pattern, $subject, $m, $flags, $offset);

        if ($res)
            return $m;

        return NULL;
    }

    /**
     * Performs a global regular expression match.
     * @param  string
     * @param  string
     * @param  int  can be PREG_OFFSET_CAPTURE (returned in bytes); PREG_SET_ORDER is default
     * @param  int  offset in bytes
     * @return array
     */
    public static function matchAll($subject, $pattern, $flags = 0, $offset = 0)
    {
        if ($offset > strlen($subject)) {
            return array();
        }

        $res = preg_match_all(
            $pattern, $subject, $m, ($flags & PREG_PATTERN_ORDER) ? $flags : ($flags | PREG_SET_ORDER), $offset
        );

        return $m;
    }

    /**
     * Perform a regular expression search and replace.
     * @param  string
     * @param  string|array
     * @param  string|callback
     * @param  int
     * @return string
     */
    public static function replace($subject, $pattern, $replacement = NULL, $limit = -1)
    {

        if (is_object($replacement) || is_array($replacement)) {
            if ($replacement instanceof Callback) {
                $replacement = $replacement->getNative();
            }
            if (!is_callable($replacement, FALSE, $textual)) {
                throw new Error("Callback '$textual' is not callable.");
            }
            $res = preg_replace_callback($pattern, $replacement, $subject, $limit);
            return $res;

        } elseif ($replacement === NULL && is_array($pattern)) {
            $replacement = array_values($pattern);
            $pattern = array_keys($pattern);
        }
        $res = preg_replace($pattern, $replacement, $subject, $limit);
        return $res;
    }

    /**
     * Заменяет русикй язык на транслит.
     * @param  string
     * @return string
     */
    public static function translit($string)
    {
        $cyr = array(
            'Щ', 'Ш', 'Ч', 'Ц', 'Ю', 'Я', 'Ж', 'А', 'Б', 'В',
            'Г', 'Д', 'Е', 'Ё', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н',
            'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ь', 'Ы', 'Ъ',
            'Э', 'Є', 'Ї', 'І',
            'щ', 'ш', 'ч', 'ц', 'ю', 'я', 'ж', 'а', 'б', 'в',
            'г', 'д', 'е', 'ё', 'з', 'и', 'й', 'к', 'л', 'м', 'н',
            'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ь', 'ы', 'ъ',
            'э', 'є', 'ї', 'і'
        );
        $lat = array(
            'shch', 'sh', 'ch', 'c', 'yu', 'ya', 'j', 'a', 'b', 'v',
            'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n',
            'o', 'p', 'r', 's', 't', 'u', 'f', 'h', '',
            'y', '', 'e', 'e', 'yi', 'i',
            'shch', 'sh', 'ch', 'c', 'yu', 'ya', 'j', 'a', 'b', 'v',
            'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n',
            'o', 'p', 'r', 's', 't', 'u', 'f', 'h',
            '', 'y', '', 'e', 'e', 'yi', 'i'
        );
        for ($i = 0; $i < count($cyr); $i++) {
            $c_cyr = $cyr[$i];
            $c_lat = $lat[$i];
            $string = str_replace($c_cyr, $c_lat, $string);
        }

        $string = preg_replace(
            '/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]e/', '\${1}e', $string);

        $string = preg_replace(
            '/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]/', "\${1}'", $string);
        $string = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $string);
        $string = preg_replace("/^kh/", "h", $string);
        $string = preg_replace("/^Kh/", "H", $string);
        return $string;
    }

    /**
     * Функция ord() для мультибайтовы строк
     *
     * @param string $c символ utf-8
     * @return int код символа
     */
    public static function uniord($c)
    {
        $h = ord($c{0});
        if ($h <= 0x7F) {
            return $h;
        } else if ($h < 0xC2) {
            return false;
        } else if ($h <= 0xDF) {
            return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
        } else if ($h <= 0xEF) {
            return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
            | (ord($c{2}) & 0x3F);
        } else if ($h <= 0xF4) {
            return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
            | (ord($c{2}) & 0x3F) << 6
            | (ord($c{3}) & 0x3F);
        } else {
            return false;
        }
    }

    /**
     * Функция chr() для мультибайтовы строк
     *
     * @param int $c код символа
     * @return string символ utf-8
     */
    public static function unichr($c)
    {
        if ($c <= 0x7F) {
            return chr($c);
        } else if ($c <= 0x7FF) {
            return chr(0xC0 | $c >> 6) . chr(0x80 | $c & 0x3F);
        } else if ($c <= 0xFFFF) {
            return chr(0xE0 | $c >> 12) . chr(0x80 | $c >> 6 & 0x3F)
            . chr(0x80 | $c & 0x3F);
        } else if ($c <= 0x10FFFF) {
            return chr(0xF0 | $c >> 18) . chr(0x80 | $c >> 12 & 0x3F)
            . chr(0x80 | $c >> 6 & 0x3F)
            . chr(0x80 | $c & 0x3F);
        } else {
            return false;
        }
    }

    public static function escape($text, $ENT_QUOTES = false)
    {
        $text = str_replace("&", "&amp;", $text);
        $text = str_replace("<", "&lt;", $text);
        $text = str_replace(">", "&gt;", $text);
        $text = str_replace('"', "&quot;", $text);
        if ($ENT_QUOTES)
            $text = str_replace("'", "&apos;", $text);
        $text = addslashes($text);
        return $text;
    }


    public static function unescape($text)
    {
        $text = str_replace("&amp;", "&", $text);
        $text = str_replace("&lt;", "<", $text);
        $text = str_replace("&gt;", ">", $text);
        $text = str_replace("&quot;", '"', $text);
        $text = str_replace("&apos;", "'", $text);
        $text = stripcslashes($text);
        return $text;
    }

    public static function escape_array($var)
    {
        if (is_array($var)) {
            $new = array();
            foreach ($var as $k => $v) {
                $new[self::escape_array($k)] = self::escape_array($v);
            }
            $var = $new;
        } elseif (is_object($var)) {
            $vars = get_object_vars($var);
            foreach ($vars as $m => $v) {
                $var->$m = self::escape_array($v);
            }
        } elseif (is_string($var)) {
            $var = self::escape($var);
        }
        return $var;
    }

    public static function unescape_array($var)
    {
        if (is_array($var)) {
            $new = array();
            foreach ($var as $k => $v) {
                $new[self::unescape_array($k)] = self::unescape_array($v);
            }
            $var = $new;
        } elseif (is_object($var)) {
            $vars = get_object_vars($var);
            foreach ($vars as $m => $v) {
                $var->$m = self::unescape_array($v);
            }
        } elseif (is_string($var)) {
            $var = self::unescape($var);
        }
        return $var;
    }

    public static function codePassword($pass, $type = 'default')
    {
        switch ($type) {
            case 'base64':
                return base64_encode(pack('H*', sha1($pass)));
                break;
            case 'whirlpool':
                return base64_encode(hash('whirlpool', $pass, True));
                break;
            default:
                return md5(md5($pass));
        }
    }

    /**
     * ($form1 = 1 товар,$form2 = 2 товара,$form5 = 5 товаров,
     * @return string
     */
    public static function pluralForm($n, $form1, $form2, $form5)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $form5;
        if ($n1 > 1 && $n1 < 5) return $form2;
        if ($n1 == 1) return $form1;
        return $form5;
    }

    /**
     * @return string  String::format("string {0} {1}", value0 ,value1)
     */
    public static function format()
    {
        $numbers = func_num_args();
        if ($numbers == 0) {
            return '';
        }
        if ($numbers == 1) {
            return func_get_arg(0);
        }
        $list = func_get_args();
        $str = array_shift($list);
        foreach ($list as $num => $value) {
            $str = str_replace('{' . $num . '}', $value, $str);
        }
        return $str;
    }

    /**
     * Перегоняет строку вида : ООО "Рога и копыта" на : ООО «Рога и копыта»
     * @param $string
     * @return mixed
     */
    public static function title($string)
    {
        $string = preg_replace('~"([^"]*)"~', '&laquo;\1&raquo;', $string);
        $string = str_replace('"', '&quot;', $string);
        return $string;
    }


    /**
     * @param string $sQuery
     * @param int $length
     * @return array
     */
    public static function parserKeywords($sQuery, $length = 4)
    {
        $aKeyword = $iKeyword = array();
        $sQuery = preg_replace('%[^\w]%ui', ' ', $sQuery);
        $aRequestString = preg_split('/[\s,]+/', $sQuery, 5);
        if ($aRequestString) {
            foreach ($aRequestString as $sValue) {
                $sValue = self::trim($sValue);
                if (self::length($sValue) > $length) {
                    $crop = self::cropVowels($sValue, $length);
                    $crop = Strings::lower($crop);
                    if (!isset($iKeyword[md5($crop)])) {
                        $aKeyword[] = $crop;
                        $iKeyword[md5($crop)] = true;
                    }
                }
            }
        }
        if (count($aKeyword) == 0) {
            $aKeyword[] = $sQuery;
        }
        return $aKeyword;
    }

    /**
     * Обрезает слова убираея в окончания гласные
     * @param $word
     * @param int $length
     * @return string
     */
    public static function cropVowels($word, $length = 3)
    {
        $tmp_word = $word;
        $vowel = array('А', 'а', 'Е', 'е', 'И', 'и', 'Й', 'й', 'О', 'о', 'У', 'у', 'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я', 'Х', 'х', 'В', 'в', 'М', 'м');
        if (strlen($word) > $length) {
            $aWord = self::str_split_utf8($word);
            $count = count($aWord) - 1;
            while (array_search($aWord[$count], $vowel) !== false) {
                unset($aWord[$count]);
                $count--;
                if (count($aWord) <= $length) {
                    break;
                }
            }
            $word = implode('', $aWord);
            if (strlen($word) <= $length + 2) {
                return $tmp_word;
            }
        }
        return $word;
    }

    public static function str_split_utf8($str)
    {
        $split = 1;
        $array = array();
        for ($i = 0; $i < strlen($str);) {
            $value = ord($str[$i]);
            if ($value > 127) {
                if ($value >= 192 && $value <= 223) $split = 2;
                elseif ($value >= 224 && $value <= 239) $split = 3;
                elseif ($value >= 240 && $value <= 247) $split = 4;
            } else $split = 1;
            $key = NULL;
            for ($j = 0; $j < $split; $j++, $i++) $key .= $str[$i];
            array_push($array, $key);
        }
        return $array;
    }

    /**
     * Парсер текста и случайных вариантов
     * Пример: [[Сегодня [утром|после обеда]]|Вчера] я [побежал|пошел|поехал[ на автобусе| на машине| на [трамвае|троллейбусе]|]]
     * @param string $text
     * @return string
     */
    public static function textGenerator($text)
    {
        while (preg_match('#\[([^\[\]]+)\]#i', $text, $m)) {
            $v = explode('|', $m[1]);
            $i = rand(0, count($v) - 1);
            $text = preg_replace('#' . preg_quote($m[0]) . '#i', $v[$i], $text, 1);
        }
        return $text;
    }

    /**
     * Парсер текста и выдает все варианты случайности
     * @param $text
     * @param bool $clean
     * @return array
     */
    public static function textsGeneratorAll($text, $clean = true, $count = false)
    {
        static $result;
        if ($clean) {
            $result = array();
        }
        if ($count) {
            if (count($result) >= $count) {
                return array_values(array_unique($result));
            }
        }

        if (preg_match("/^(.*)\[([^\[\]]+)\](.*)$/isU", $text, $matches)) {
            $p = explode('|', $matches[2]);
            foreach ($p as $comb) self::textsGeneratorAll($matches[1] . $comb . $matches[3], false, $count);
        } else {
            $result[] = $text;
        }

        return array_values(array_unique($result));
    }

    /**
     * Generate unique ID to cache rates calculation results
     *
     * @param mixed parameters to generate unique ID from
     * @return mixed array with rates if calculated, false otherwise
     */
    public static function cached_rate_id()
    {
        return md5(serialize(func_get_args()));
    }

    /**
     * Generator crc32
     * @param $key
     * @return string
     */
    public static function crc32($key)
    {
        return sprintf('%u', crc32($key));
    }

    public static function num2str($num)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('копейка', 'копейки', 'копеек', 1),
            array('рубль', 'рубля', 'рублей', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );

        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = self::pluralForm($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = self::pluralForm(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . self::pluralForm($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

}