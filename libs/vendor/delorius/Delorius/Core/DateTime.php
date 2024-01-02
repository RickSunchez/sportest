<?php
namespace Delorius\Core;
use Delorius\Exception\Error;


/**
 * DateTime with serialization and timestamp support for PHP 5.2.
 */
class DateTime extends \DateTime
{
    /** minute in seconds */
    const MINUTE = 60;

    /** hour in seconds */
    const HOUR = 3600;

    /** day in seconds */
    const DAY = 86400;

    /** week in seconds */
    const WEEK = 604800;

    /** average month in seconds */
    const MONTH = 2629800;

    /** average year in seconds */
    const YEAR = 31557600;


    /**
     * DateTime object factory.
     * @param  string|int|\DateTime
     * @return DateTime
     */
    public static function from($time)
    {
        if ($time instanceof \DateTime || $time instanceof \DateTimeInterface) {
            return new static($time->format('Y-m-d H:i:s'), $time->getTimezone());
        } elseif (is_numeric($time)) {
            if ($time <= self::YEAR) {
                $time += time();
            }
            return new static(date('Y-m-d H:i:s', $time));
        } else {
            return new static($time);
        }
    }


    public function __toString()
    {
        return $this->format('Y-m-d H:i:s');
    }


    public function modifyClone($modify = '')
    {
        $dolly = clone $this;
        return $modify ? $dolly->modify($modify) : $dolly;
    }

    /**
     * @param  int
     * @return self
     */
    public function setTimestamp($timestamp)
    {
        $zone = $this->getTimezone();
        $this->__construct('@' . $timestamp);
        return $this->setTimeZone($zone);
    }


    /**
     * @return int|string
     */
    public function getTimestamp()
    {
        $ts = $this->format('U');
        return is_float($tmp = $ts * 1) ? $ts : $tmp;
    }

    /**
     * Returns new DateTime object formatted according to the specified format.
     * @param string The format the $time parameter should be in
     * @param string String representing the time
     * @param string|\DateTimeZone desired timezone (default timezone is used if NULL is passed)
     * @return self|FALSE
     */
    public static function createFromFormat($format, $time, $timezone = NULL)
    {
        if ($timezone === NULL) {
            $timezone = new \DateTimeZone(date_default_timezone_get());

        } elseif (is_string($timezone)) {
            $timezone = new \DateTimeZone($timezone);

        } elseif (!$timezone instanceof \DateTimeZone) {
            throw new Error('Invalid timezone given');
        }

        $date = parent::createFromFormat($format, $time, $timezone);
        return $date ? static::from($date) : FALSE;
    }


    static $month_str = array(
        'января', 'февраля', 'марта',
        'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября',
        'октября', 'ноября', 'декабря'
    );

    static $aMonth = array(
        1 => array('M' => 'января', 'm' => 'янв', 'self' => 'январь'),
        2 => array('M' => 'февраля', 'm' => 'фев', 'self' => 'февраль'),
        3 => array('M' => 'марта', 'm' => 'мар', 'self' => 'март'),
        4 => array('M' => 'апреля', 'm' => 'апр', 'self' => 'апрель'),
        5 => array('M' => 'мая', 'm' => 'май', 'self' => 'май'),
        6 => array('M' => 'июня', 'm' => 'июн', 'self' => 'июнь'),
        7 => array('M' => 'июля', 'm' => 'июл', 'self' => 'июль'),
        8 => array('M' => 'августа', 'm' => 'авг', 'self' => 'август'),
        9 => array('M' => 'сентября', 'm' => 'сен', 'self' => 'сентябрь'),
        10 => array('M' => 'октября', 'm' => 'окт', 'self' => 'октябрь'),
        11 => array('M' => 'ноября', 'm' => 'ноя', 'self' => 'ноябрь'),
        12 => array('M' => 'декабря', 'm' => 'дек', 'self' => 'декабрь')
    );

    static $month_str_self = array(
        'Январь', 'Февраль', 'Март',
        'Апрель', 'Май', 'Июнь',
        'Июль', 'Август', 'Сентябрь',
        'Октябрь', 'Ноябрь', 'Декабрь'
    );

    static $month_int = array(
        '01', '02', '03',
        '04', '05', '06',
        '07', '08', '09',
        '10', '11', '12'
    );

    public static function dateFormat($date, $is_time = false)
    {
        $day = date('Y-m-d', $date);
        // получаем значение даты и времени

        switch ($day) {
            // Если дата совпадает с сегодняшней
            case date('Y-m-d'):
                $result = 'Сегодня';
                break;

            //Если дата совпадает со вчерашней
            case date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))):
                $result = 'Вчера';
                break;

            default:
                {
                // Разделяем отображение даты на составляющие
                list($y, $m, $d) = explode('-', $day);
                // Замена числового обозначения месяца на словесное (склоненное в падеже)
                $m = str_replace(self::$month_int, self::$month_str, $m);
                // Формирование окончательного результата
                $result = $d . ' ' . $m . ' ' . $y;
                }
        }

        if ($is_time) {
            // Получаем отдельные составляющие времени
            // Секунды нас не интересуют
            $time = date('H:i:s', $date);
            list($h, $m, $s) = explode(':', $time);
            $result .= ' в ' . $h . ':' . $m;
        }
        return $result;
    }


    public static function getMonth($num, $str = 'self')
    {
        return self::$aMonth[(int)$num][$str];
    }


}
