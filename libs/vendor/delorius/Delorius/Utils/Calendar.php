<?php
namespace Delorius\Utils;

use Delorius\Core\Object;
use Delorius\View\Html;


/**
 * Опция отображения календаря. Отображать навигацию
 */
define("CLD_NAVIGATION", 1);

/**
 * Опция отображения календаря. Убрать навигацию
 */
define("CLD_NOT_NAVIGATION", 2);

/**
 * Опция отображения календаря. Определяет, использовать ли переносы строки
 * в генерируемом HTML коде. Необходимо только для отладки. По-умолчанию
 * переносы выключены.
 */
define("CLD_BREAKS", 8);


class Calendar extends Object
{

    /**
     * Массив для хранения ссылок, ассоциированных с датами.
     * @var array
     */
    protected $dateLinks = array();
    /**
     * Массив для хранения хинтов, ассоциированных с датами.
     * @var array
     */
    protected $dateTitles = array();

    /**
     * Массив с названиями месяцев
     * @var array
     */
    protected $months = array(
        1 => 'Январь', 'Февраль', 'Март',
        'Апрель', 'Май', 'Июнь',
        'Июль', 'Август', 'Сентябрь',
        'Октябрь', 'Ноябрь', 'Декабрь'
    );

    /**
     * Массив с сокращёнными названиями дней недели
     * @var array
     */
    protected $week = array(1 => 'пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс');

    /**
     * Префикс, используемый для имён всех CSS классов
     * @var string
     */
    protected $cssPrefix;

    /**
     * Дата данные для дополнительных настроек
     * @var array
     */
    protected $data = array();

    /**
     * Опции отображения календаря. Значение переменной задаётся с помощью
     * @var bool
     */
    protected $options = false;

    public function __construct($cssPrefix = '')
    {
        $this->setCssPrefix($cssPrefix);
    }

    /**
     * @param $cssPrefix
     * @return $this
     */
    public function setCssPrefix($cssPrefix)
    {
        $this->cssPrefix = $cssPrefix;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function addData(array $data)
    {
        $this->data += $data;
        return $this;
    }

    /**
     * Задаёт параметры календаря, перечисляемые через двоичное OR
     * (см. константы CLD_*).
     * @param string $options Параметры, заданные через двоичное OR.
     * @return $this
     * @example $clndr->setOptions(CLD_NAVIGATION | CLD_BREAKS)
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Добавляет гиперссылку для заданной даты. Если ссылка была уже задана,
     * она будет переопределена.
     * @param $date
     * @param $url
     * @return $this
     */
    public function addUDateLink($date, $url)
    {
        $d = getdate($date);
        $d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
        $this->dateLinks[$d] = $url;
        return $this;
    }

    /**
     * Добавляет гиперссылку для заданной даты. Отличается от addUDateLink
     * только форматом, в котором определяется дата. Если ссылка была уже
     * задана, она будет переопределена.
     * @param $day
     * @param $month
     * @param $year
     * @param $url
     * @return $this
     */
    public function addDateLink($day, $month, $year, $url)
    {
        return $this->addUDateLink(mktime(0, 0, 0, $month, $day, $year), $url);
    }


    /**
     * Удаляет ссылку для заданной даты (если она определена).
     * @access public
     * @param int $date Дата, для которой необходимо удалить ссылку (имеет
     * значение год, месяц и день)
     * @return $this
     */
    public function removeUDateLink($date)
    {
        $d = getdate($date);
        $d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
        unset($this->dateLinks[$d]);
        return $this;
    }

    /**
     * Удаляет ссылку для заданной даты (если она определена). Отличается
     * от removeUDateLink только форматом, в котором определяется дата.
     * @access public
     * @param int $day Число
     * @param int $month Месяц
     * @param int $year Год
     * @return $this
     */
    public function removeDateLink($day, $month, $year)
    {
        return $this->removeUDateLink(mktime(0, 0, 0, $month, $day, $year));
    }

    /**
     * Удаляет все определённые ссылки.
     * @return $this
     */
    public function removeDateLinks()
    {
        $this->dateLinks = array();
        return $this;
    }


    /**
     * Добавляет хинт для заданной даты. Если хинт был уже задан, он будет
     * переопределен.
     * @param int $date Дата в формате Unix timestamp, к которой будет
     * привязан хинт. Можно задавать "неточную" дату, значение часов, минут
     * и секунд игнорируется.
     * @param string $title Текст хинта
     * @return $this
     */
    public function addUDateTitle($date, $title)
    {
        $d = getdate($date);
        $d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
        $this->dateTitles[$d] = $title;
        return $this;
    }

    /**
     * Добавляет хинт для заданной даты. Отличается от addUDateTitle только
     * форматом, в котором определяется дата.
     * @param int $day Число
     * @param int $month Месяц
     * @param int $year Год
     * @param string $title Текст хинта
     * @return $this
     */
    public function addDateTitle($day, $month, $year, $title)
    {
        return $this->addUDateTitle(mktime(0, 0, 0, $month, $day, $year), $title);
    }

    /**
     * Удаляет хинт для заданной даты (если он определен).
     * @param int $date Дата, для которой необходимо удалить ссылку
     * (имеет значение год, месяц и день)
     * @return $this
     */
    public function removeUDateTitle($date)
    {
        $d = getdate($date);
        $d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
        unset($this->dateTitles[$d]);
        return $this;
    }

    /**
     * Удаляет хинт для заданной даты (если он определен). Отличается
     * от removeUDateTitle только форматом, в котором определяется дата.
     * @access public
     * @param int $day Число
     * @param int $month Месяц
     * @param int $year Год
     * @return $this
     */
    public function removeDateTitle($day, $month, $year)
    {
        return $this->removeUDateTitle(mktime(0, 0, 0, $month, $day, $year));
    }

    /**
     * Удаляет все определённые хинты.
     * @access public
     * @return $this
     */
    public function removeDateTitles()
    {
        $this->dateTitles = array();
        return $this;
    }

    /**
     * Генерирует HTML код для заданного месяца. Дата задаётся в формате Unix
     * tiestamp.
     * @param int $date Месяц, для которого необходимо сгенерировать
     * календарь, задаваемый формате Unix timestamp (значение года, само собой,
     * тоже имеет значение; часы, минуты и секунды - игнорируются)
     * @param bool $mark Флаг, определяющий необходимость выделить текущую
     * дату. По-умолчанию, текущая дата не выделяется.
     * @return string Сгенерированный HTML код
     */
    public function genUMonth($date, $mark = false)
    {
        $d = getdate($date);
        $month = $d["mon"];
        $day = $d["mday"];
        $year = $d["year"];
        $wDay = date('N', mktime(0, 0, 0, $month, 1, $year));


        $container = Html::el('div', array(
            'class' => array($this->cssPrefix . 'df-calendar')
        ));
        $container->data = $this->data;
        $container->data['day'] = $d["mday"];
        $container->data['month'] = $d["mon"];
        $container->data['year'] = $d["year"];

        /** HEADER **/
        $b_header = Html::el('div', array(
            'class' => $this->cssPrefix . 'header'
        ));
        $b_header->setHtml(
            _sf('{0} {1}', $this->months[$month], $year)
        );
        if ($this->options & CLD_NAVIGATION) {

            $pre = Html::el('a', array(
                'class' => $this->cssPrefix . 'pre',
            ))
                ->href('javascript:{}');
            $pre_month = $month - 1;
            $pre_year = $year;
            if (!$pre_month) {
                $pre_month = 12;
                $pre_year -= 1;
            }
            $pre->data['date'] = _sf('{0}-{1}', Strings::padLeft($pre_month, 2, '0'), $pre_year);
            $b_header->add($pre);

            $next = Html::el('a', array(
                'class' => $this->cssPrefix . 'next',
            ))
                ->href('javascript:{}');

            $next_month = $month + 1;
            $next_year = $year;
            if ($next_month == 13) {
                $next_month = 1;
                $next_year += 1;
            }
            $next->data['date'] = _sf('{0}-{1}', Strings::padLeft($next_month, 2, '0'), $next_year);
            $b_header->add($next);
        }
        $container->add($b_header);

        /** WEEKER **/
        $b_weeks = Html::el('div', array(
            'class' => $this->cssPrefix . 'week'
        ));
        for ($i = 1; $i <= 7; $i++) {
            $weekEnd = (($i == 6) || ($i == 7)) ? 'output' : '';
            $b_week = Html::el('div', array(
                'class' => array(
                    $this->cssPrefix . 'w' . $i,
                    $this->cssPrefix . (($i % 2) ? 'odd' : 'even'),
                    $weekEnd
                ),
            ))
                ->setText($this->week[$i]);
            $b_weeks->add($b_week);
        }
        $container->add($b_weeks);
        /** DAYS **/
        $b_days = Html::el('div', array(
            'class' => $this->cssPrefix . 'days'
        ));
        $wcnt = 0;
        /** пустые дни в недели перед началом месяца */
        if ($wDay == 1) {
            $wcnt++;
        }
        $last_month = $month - 1;
        $last_year = $year;
        if (!$last_month) {
            $last_month = 12;
            $last_year -= 1;
        }
        $max = date('t', mktime(0, 0, 0, $last_month, 1, $last_year));
        $last_days = $wDay == 1 ? 7 : $wDay - 1;
        $max -= $last_days;
        $count = $wDay == 1 ? 8 : $wDay;
        for ($i = 1; $i < $count; $i++) {
            $weekEnd = (($i == 6) || ($i == 7)) ? 'output' : '';
            $b_day = Html::el('div', array(
                'class' => array(
                    $this->cssPrefix . 'out',
                    $this->cssPrefix . (($i % 2) ? 'odd' : 'even'),
                    $weekEnd
                ),
            ))
                ->setHtml(++$max);
            $b_days->add($b_day);
        }

        for ($i = 1, $striper = $wDay % 2; $i <= date('t', $date);
             $i++, $striper = !$striper) {
            // отмечаем текущий день, если надо
            $today = $mark && ($i == $day);
            // добавляем линки
            $d = mktime(0, 0, 0, $month, $i, $year);
            $linkSet = isset($this->dateLinks[$d]);
            $titleSet = isset($this->dateTitles[$d]);
            $wDay = ($wDay + 1) % 7;
            $weekEnd = (($wDay == 1) || ($wDay == 0)) ? 'output' : '';

            $b_day = Html::el('div', array(
                'class' => array(
                    $this->cssPrefix . (($i % 2) ? 'odd' : 'even'),
                    $weekEnd,
                    $today ? 'today' : ''
                ),
                'data'=>array('day'=>$i,'unixtime'=>$d)
            ));

            if ($linkSet && $titleSet) {
                $inner = Html::el('a', array(
                    'class' => $this->cssPrefix . 'titleddatelink',
                    'title' => $this->dateTitles[$d]
                ))
                    ->href($this->dateLinks[$d])
                    ->setText($i);
            } elseif ($linkSet) {
                $inner = Html::el('a', array(
                    'class' => $this->cssPrefix . 'datelink',
                    'data' => array('title',$this->dateTitles[$d])
                ))
                    ->href($this->dateLinks[$d])
                    ->setText($i);
            } elseif ($titleSet) {
                $inner = Html::el('em', array(
                    'title' => $this->dateTitles[$d],
                    'class' => $this->cssPrefix . 'titleddate'
                ))
                    ->setText($i);
            } else {
                $inner = $i;
            }
            $b_day->setHtml($inner);

            if ($wDay == 1) {
                $wcnt++;
                $striper = true;
            }
            $b_days->add($b_day);
        }


        if ($wDay == 0) {
            $wDay = 6;
        } else {
            $wDay -= 1;
        }

        $spacesNum = 7 - $wDay;
        if ($spacesNum) {
            $wcnt++;
        }

        for ($i = 1; $i <= $spacesNum; $i++, $striper = !$striper) {

            $b_day = Html::el('div', array(
                'class' => array(
                    $this->cssPrefix . 'out',
                    $this->cssPrefix . (($striper % 2) ? 'odd' : 'even'),
                    'num_' . $spacesNum
                ),
            ))
                ->setHtml($i);
            $b_days->add($b_day);
        }


        if ($wcnt <= 5) {
            $j = $i;
            for ($i = 1; $i <= 7; $i++) {
                $weekEnd = (($i == 6) || ($i == 7)) ? 'output' : '';
                $b_day = Html::el('div', array(
                    'class' => array(
                        $this->cssPrefix . 'out',
                        $this->cssPrefix . (($i % 2) ? 'odd' : 'even'),
                        $weekEnd
                    ),
                ))
                    ->setHtml($j++);
                $b_days->add($b_day);
            }
        }

        $container->add($b_days);

        return $container;
    }


    /**
     * Генерирует HTML код для заданного месяца из заданного года.
     * @access public
     * @param mixed $day Если параметру задать числовое значение, заданная
     * дата будет выделена. Если задать значение false - выделение выполнено
     * не будет.
     * @param int $month Месяц, для которого необходимо сгенерировать
     * календарь.
     * @param int $year Год, к которому относится заданный в предыдущем
     * параметре месяц.
     * @return string Сгенерированный HTML код
     */
    function genMonth($day, $month, $year)
    {
        return $this->genUMonth(mktime(0, 0, 0, $month, is_numeric($day) ? $day : 1, $year), is_numeric($day) ? true : false);
    }

    /**
     * Генерирует HTML код для заданного года
     * @param int $date Год, для которого необходимо сгенерировать календарь
     * в формате Unix timestamp (значениа месяца часов, минут и секунд
     * игнорируются)
     * @param int $mark Флаг, определяющий необходимость выделить текущую
     * дату. По-умолчанию, текущая дата не выделяется.
     * @param int $width Количество месяцев, отображемых в один ряд.
     * По-умолчанию - 3.
     * @return string Сгенерированный HTML код
     */
    function genUYear($date, $mark = false, $width = 3)
    {
        $this->setOptions(CLD_NOT_NAVIGATION);
        $year = date('Y', $date);
        $mMonth = date('n', $date);
        $res[] = '<table border="0" cellspacing="0" cellpadding="10" class="' .
            $this->cssPrefix . 'year">';
        $res[] = '<tr><th colspan="' . $width . '" class="' . $this->cssPrefix .
            'yeartitle"><b>' . $year . '</b></th></tr>';
        $res[] = '<tr>';
        for ($i = 1; $i <= 12; $i++) {
            if ($mMonth == $i) {
                $monthHtml = $this->genUMonth($date, $mark);
            } else {
                $monthHtml = $this->genUMonth(mktime(0, 0, 0, $i, 1, $year));
            }
            $res[] = '<td valign="top" class="' . $this->cssPrefix .
                (($i % $width) ? 'monthcell' : 'monthlastcell') . '">' . $monthHtml .
                '</td>';
            if (!($i % $width)) {
                $res[] = '</tr><tr>';
            }
        }
        $res[] = '</tr>';
        $res[] = '</table>';
        // опредляем, нужен ли разделитель для строк HTML кода
        $s = ($this->options & CLD_BREAKS) ? "\n" : '';
        return implode($s, $res);
    }

    /**
     * Генерирует HTML код для заданного года. Отличается от genUYear() только
     * форматом задания даты.
     * @access public
     * @param int $day Число (имеет значение только в том случае, если его
     * необходимо выделить; в противном случае, можно задать false)
     * @param int $month Месяц (имеет значение только в том случае, если
     * необходимо выделить заданное число; в противном случае, можно задать
     * false)
     * @param int $year Год, для которого необходимо сгенерировать календарь
     * @param int $mark Флаг, определяющий необходимость выделить текущую
     * дату. По-умолчанию, текущая дата не выделяется.
     * @param int $width Количество месяцев, отображемых в один ряд.
     * По-умолчанию - 3.
     * @return string Сгенерированный HTML код
     */
    function genYear($day, $month, $year, $mark = false, $width = 3)
    {
        return $this->genUYear(mktime(0, 0, 0, is_numeric($month) ? $month : 1,
            is_numeric($day) ? $day : 1, $year), $mark, $width);
    }


} 