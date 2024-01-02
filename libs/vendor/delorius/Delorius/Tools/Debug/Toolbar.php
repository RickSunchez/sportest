<?php
namespace Delorius\Tools\Debug;

use Delorius\Core\Common;
use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Strings;
use Delorius\View\View;

class Toolbar extends Object
{

    /**
     * Can we render toolbar?
     *
     * @var bool
     */
    protected static $_enabled = false;

    /**
     * @var string|null
     */
    protected static $_secret_key = null;

    /**
     * @param $key
     */
    public static function setSecretKey($key)
    {
        self::$_secret_key = $key;
    }

    /**
     * @param string $token
     */
    public static function header($token)
    {
        list($time, $memory) = Profiler::total($token);

        $time = number_format($time, 6) . ' sec'; //sec
        $memory = number_format($memory / 1024, 4) . ' kB'; //kB

        Environment::getContext()->getService('httpResponse')->addHeader('profiler-' . Strings::webalize($token), _sf('time={0}; memory={1}', $time, $memory));
    }

    public static function render($print = FALSE)
    {
        $view = new View();
        $html = $view->load(dirname(__FILE__) . '/view/stats', array(), true);

        if ($print) {
            echo $html;
            return;
        }

        $date = date('d_m_Y_H_i_s');
        $upload = Environment::getContext()->getParameters('path.upload');
        $path = $upload . '/toolbar/';
        FileSystem::write($path . $date . '.html', $html);
    }

    /**
     * Disable toolbar
     * @static
     */
    public static function disable()
    {
        self::$_enabled = FALSE;
    }

    /**
     * Enable toolbar
     * @static
     */
    public static function enable()
    {
        self::$_enabled = TRUE;
    }

    /**
     * @static
     * @return bool
     */
    public static function is_enabled()
    {
        $get = Environment::getContext()->getService('httpRequest')->getRequest();
        // Auto render if secret key isset
        if (self::$_secret_key !== FALSE AND isset($get[self::$_secret_key])) {
            return TRUE;
        }

        if (self::$_enabled === FALSE) {
            return FALSE;
        }

        return TRUE;
    }

} 