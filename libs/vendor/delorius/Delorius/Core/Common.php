<?php
namespace Delorius\Core;

use Delorius\Exception\Error;
use Delorius\Utils\Arrays;


Class Common
{


    private function __construct()
    {       
    }

    /*
     * использовать для контролеров
     * @var string {Project}:{Bundle}:{Controller}:{Action}
     * @class =  \{Project}\{Bundle}\Controller\{Controller}Controller
     * @action = {Action}
     * @return array('class'=>class,'action'=>action)
     */
    public static function getController($class_str)
    {
        $array = explode(':', $class_str);
        $num_action = count($array) - 1;
        $num_controller = $num_action - 1;
        $class = '';
        $action = '';
        if (is_array($array))
            foreach ($array as $key => $name) {
                if ($num_action == $key)
                    $action = $name;
                elseif ($num_controller == $key)
                    $class .= _sf('\Controller\{0}Controller', $name);
                else
                    $class .= _sf('\{0}', $name);
            }

        return array(
            'class' => $class,
            'action' => $action
        );

    }

    /*
     * использовать для подключения конфиг файлов
     * @var string {Project}:{Bundle}
     * @config \{Project}\{Bundle}\_settings_\config.php;
     * @return array
     */

    protected static $arConfig = array();

    public static function getConfig($class_str, $arr_path = null)
    {
        list($project, $bundle) = explode(':', $class_str);
        if (empty(self::$arConfig[$project][$bundle])) {
            $config = include_once DIR_INDEX.'/libs'. _sf('/project/{0}/{1}/_settings_/config.php', $project, $bundle);
            self::$arConfig[$project][$bundle] = $config;
        }
        if ($arr_path) {
            return Arrays::get(self::$arConfig[$project][$bundle], $arr_path);
        }
        return self::$arConfig[$project][$bundle];
    }

    /*
     * использовать для подключения роутинга для бандла
     * @var string {Project}:{Bundle}
     * @router \{Project}\{Bundle}\_settings_\router.php;
     * @return \Delorius\Routing\RouteCollection;
     */

    protected static $arRouter = array();

    public static function getRouter($class_str)
    {
        list($project, $bundle) = explode(':', $class_str);
        if (empty(self::$arRouter[$project][$bundle])) {

            $config = include_once DIR_INDEX.'/libs'. _sf('/project/{0}/{1}/_settings_/router.php', $project, $bundle);
            self::$arRouter[$project][$bundle] = $config;
        }
        return self::$arRouter[$project][$bundle];
    }


    /*
     * использовать для подключения роутинга для бандла
     * @var string {Project}:{Bundle}
     * @router \{Project}\{Bundle}\_settings_\init.php;
     * @return void
     */
    public static function includeInit($class_str)
    {
        list($project, $bundle) = explode(':', $class_str);
        include_once DIR_INDEX.'/libs'. _sf('/project/{0}/{1}/_settings_/init.php', $project, $bundle);
    }


    /*
     * использовать для подключения языковых сообщений
     * @var string {Project}:{Bundle}:{lang}
     * @config \{Project}\{Bundle}\_settings_\messages\{lang}.php;
     * @return array
     */

    protected static $arMessages = array();

    public static function getMessages($class_str)
    {
        list($project, $bundle, $lang) = explode(':', $class_str);
        if (empty(self::$arMessages[$project][$bundle][$lang])) {

            $file = DIR_INDEX.'/libs'. _sf('/project/{0}/{1}/_settings_/messages/{2}.php', $project, $bundle, $lang);
            if (file_exists($file))
                $config = include_once $file;
            else
                throw new Error(_sf('Not found translation file: {0}', $class_str));
            self::$arMessages[$project][$bundle][$lang] = $config;
        }
        return self::$arMessages[$project][$bundle][$lang];
    }
}
