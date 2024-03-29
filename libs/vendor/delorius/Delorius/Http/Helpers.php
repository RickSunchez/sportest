<?php
namespace Delorius\Http;


use Delorius\Core\DateTime;

class Helpers
{

    /**
     * Returns HTTP valid date format.
     * @param  string|int|\DateTime
     * @return string
     */
    public static function formatDate($time)
    {
        $time = DateTime::from($time);
        $time->setTimezone(new \DateTimeZone('GMT'));
        return $time->format('D, d M Y H:i:s \G\M\T');
    }


    /**
     * Is IP address in CIDR block?
     * @return bool
     */
    public static function ipMatch($ip, $mask)
    {
        list($mask, $size) = explode('/', $mask . '/');
        $tmp = function ($n) {
            return sprintf('%032b', $n);
        };
        $ip = implode('', array_map($tmp, unpack('N*', inet_pton($ip))));
        $mask = implode('', array_map($tmp, unpack('N*', inet_pton($mask))));
        $max = strlen($ip);
        if (!$max || $max !== strlen($mask) || $size < 0 || $size > $max) {
            return FALSE;
        }
        return strncmp($ip, $mask, $size === '' ? $max : $size) === 0;
    }


    /**
     * Removes duplicate cookies from response.
     * @return void
     * @internal
     */
    public static function removeDuplicateCookies()
    {
        if (headers_sent($file, $line) || ini_get('suhosin.cookie.encrypt')) {
            return;
        }

        $flatten = array();
        foreach (headers_list() as $header) {
            if (preg_match('#^Set-Cookie: .+?=#', $header, $m)) {
                $flatten[$m[0]] = $header;
                header_remove('Set-Cookie');
            }
        }
        foreach (array_values($flatten) as $key => $header) {
            header($header, $key === 0);
        }
    }


    /**
     * @internal
     */
    public static function stripSlashes($arr, $onlyKeys = FALSE)
    {
        $res = array();
        foreach ($arr as $k => $v) {
            $res[stripslashes($k)] = is_array($v)
                ? self::stripSlashes($v, $onlyKeys)
                : ($onlyKeys ? $v : stripslashes($v));
        }
        return $res;
    }
}