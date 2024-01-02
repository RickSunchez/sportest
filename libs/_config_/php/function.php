<?php

defined('DELORIUS') or die('access denied');

/*
 * языковая функция __('{Project}:{Bundle}','{0},  hi',$name); на выходе для RU 'Jon, привет'
 * \{Project}\{Bundle}\_settings_\messages\{language}
 * @return string
 */
function _t()
{
    $lang = DI()->getService('language');
    return call_user_func_array(array($lang, 'translate'), func_get_args());
}

/**
 * @return string  _sf("string {0} {1}", value0 ,value1)
 */
function _sf()
{
    return call_user_func_array('\Delorius\Utils\Strings::format', func_get_args());
}

function stripslashes_deep($value)
{
    $value = is_array($value) ?
        array_map('stripslashes_deep', $value) :
        stripslashes($value);
    return $value;
}

function get(&$arg)
{
    return $arg;
}

/**
 * @return \Delorius\DI\Container
 * @throws \Delorius\Exception\Error
 */
function DI()
{
    return \Delorius\Core\Environment::getContext();
}

/**
 * @param $name
 * @param array $parameters
 * @param bool|true $absoluteUrl
 * @return string
 */
function link_to($name, array $parameters = array(), $absoluteUrl = true)
{
    try {
        return DI()->getService('router')->generate($name, $parameters, $absoluteUrl);
    } catch (\Delorius\Exception\Error $e) {
        return "#${name}_error";
    }
}

/**
 * @param array $arr
 * @return string
 */
function link_to_array(array $arr = null, $absoluteUrl = true)
{
    if (!$arr)
        return '';
    $router = array_keys($arr);
    $router = array_shift($router);
    $attrs = array_values($arr);
    $attrs = array_shift($attrs);
    if (!$router) {
        $router = $attrs;
        $attrs = array();
    }
    $link = link_to($router, (array)$attrs, $absoluteUrl);
    return $link;
}

/** параметры текущего выбраного домена  */
function getHostParameter($name = null)
{
    static $domainRouter = null;
    if ($domainRouter == null) {
        $domainRouter = DI()->getService('domainRouter')->match(DI()->getService('httpRequest')->getUrl()->getAbsoluteUrlNoQuery());
    }
    if ($name != null)
        return $domainRouter[$name];
    return $domainRouter;
}

/** получаем домен для установки куки */
function getDomainCookie()
{
    static $domain = null;
    if ($domain == null) {
        $cookies = DI()->getService('session')->getCookieParameters();
        $domain = $cookies['domain'];
    }
    return $domain;
}

/** получаем канонический адрес страницы */
function getCanonical()
{
    return DI()->getService('urlScript')->getAbsoluteUrlNoQuery();
}

/**
 * @param array $resource
 * @return bool
 */
function isAllowed(array $resource)
{
    $user = DI()->getService('user');
    foreach ($resource as $res) {
        if ($user->isAllowed($res)) {
            return true;
        }
    }
    return false;
}

/**
 * @param $msg
 * @param string $status
 */
function logger($msg, $status = \Delorius\Tools\ILogger::INFO)
{
    DI()->getService('logger')->info($msg, $status);
}

/**
 * \Delorius\Utils\Callback factory.
 * @param  mixed $callback class, object, callable
 * @param  string $m method
 * @return \Delorius\Utils\Callback
 */
function callback($callback, $m = NULL)
{
    return new \Delorius\Utils\Callback($callback, $m);
}

function objectToArray($d)
{
    if (is_object($d) && isset($d->data)) {
        return $d->data;
    }

    return $d;
}

function arrayToObject($d)
{
    if (is_array($d)) {

        $object = new stdClass();
        $object->data = $d;
        return $object;
    } else {
        // Return object
        return $d;
    }
}

/**
 * @param array $params
 * @param string $name
 */
function debugHeader(array $params, $name = 'info')
{
    if (DI()->getParameters('productionMode')) {
        return;
    }
    static $i = 0;
    foreach ($params as $key => $string) {
        DI()->getService('httpResponse')->addHeader('debug-' . $name . '-' . $i, \Delorius\Utils\Strings::translit($string));
        $i++;
    }
}

$_DUMP_VAR = array();
function df_print_r()
{
    if (func_num_args() >= 1) {
        global $_DUMP_VAR;
        $list = func_get_args();
        foreach ($list as $value) {
            $_DUMP_VAR[] = $value;
        }
    } else {
        df_print_r_echo();
        $_DUMP_VAR = array();
    }
}

function df_print_r_echo()
{
    global $_DUMP_VAR;
    if (count($_DUMP_VAR)) {
        echo "<style type='text/css'>
                .df-print-r{
                    color: #333333;
                    background-color: #f5f5f5;
                    border: 1px solid #cccccc;
                }
                .df-print-value{
                    color: #333333;
                    font-size: 14px;
                    padding: 5px 0 5px 5px;
                }
            </style>";
        echo "<div class='df-print-r'>";
        foreach ($_DUMP_VAR as $value) {
            if (is_scalar($value)) {
                echo "<div class='df-print-value'>$value</div>";
            } else {
                echo "<pre class='df-print-pre'>";
                print_r($value);
                echo "</pre>";
            }
        }
        echo "</div>";
    }

}

if (!function_exists('json_last_error_msg')) {
    function json_last_error_msg()
    {
        switch (json_last_error()) {
            default:
                return;
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
        }
        return $error;
    }
}

/**
 * Проверка соотвествуия урла текущему пути
 * @param string $url
 * @return bool
 */
function is_current_path($url, $needle = false)
{
    static $current = null;
    if ($current == null) {
        $current= DI()->getService('url')->getPath();
    }

    $parser = parse_url($url);
    $path = $parser['path'];

    if ($path == $current && !$needle) {
        return true;
    } elseif ($needle && strpos($current, $path) === 0) {
        return true;
    }

    return false;
}

/**
 * @param string $name
 * @param string $path
 * @param array $query
 * @return string
 */
function snippet($name, $path, array $query = array())
{
    $parser = DI()->getService('parser');

    if (!$parser->hasSnippet($name)) {
        return '';
    }

    $class = $parser->getSnippet($name);

    /** @var \CMS\Core\Component\Snippet\AParserRenderer $parser */
    $parser = new $class($path, $query);
    $parser->before();
    return $parser->render();
}


/**
 * @param \Delorius\Core\ORM $object
 * @param null $message
 * @throws \Delorius\Exception\NotFound
 */
function load_or_404(\Delorius\Core\ORM $object, $message = null)
{
    if (!$object->loaded() || $object->status == 0) {
        throw new \Delorius\Exception\NotFound($message ? $message : 'Страница не найдена');
    }
}