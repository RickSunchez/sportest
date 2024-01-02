<?php
namespace Delorius\View;

use Delorius\Application\IController;
use Delorius\Application\UI\Control;
use Delorius\Caching\Cache;
use Delorius\Configure\Site;
use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Exception\Error;
use Delorius\Tools\Debug\Profiler;
use Delorius\Utils\Strings;

Class View extends Object
{
    /** @var \Delorius\DI\Container */
    protected $container;
    /** @var  \Delorius\Caching\Cache */
    protected $cache;
    /** @var array Глобальные данные в шаблоне */
    protected $global_data = array();
    /** @var Browser */
    protected $browser;
    /** @var Site */
    protected $site;

    public function __construct(Control $control = null)
    {
        $this->container = Environment::getContext();
        $this->site = $this->container->getService('site');
        $this->browser = $this->container->getService('browser');
        $this->cache = $this->container->getService('cache')->derive('html');

        $this->addGlobalData(array(
            'site' => $this->site,
            'browser' => $this->browser,
            'cache' => $this->cache,
            'control' => $control
        ));
    }

    /**
     * @param array $array
     * @return $this
     */
    public function addGlobalData(array $array)
    {
        $this->global_data = $array + $this->global_data;
        return $this;
    }

    /**
     * @param $template
     * @param array $_attr
     * @param bool $isAbsolute
     * @return string
     */
    public function load($template, array $_attr = null, $isAbsolute = false)
    {
        if ($_attr) {
            extract($_attr, EXTR_SKIP);
        }
        if ($this->global_data) {
            extract($this->global_data, EXTR_SKIP | EXTR_REFS);
        }
        $_tpl = $this->hasTemplate($template, $isAbsolute);
        if ($_tpl === false) {
            return '';
        }
        ob_start();
        include $_tpl;
        $_out = ob_get_contents();
        ob_end_clean();
        return $_out;
    }

    /**
     * \CMS\Core\SomeClass->someMethodPartial()
     * @param $strClass
     * @param array $option
     * @param bool|false $cache
     * @param string $lifetime
     * @return mixed|NULL|string
     */
    public function action($strClass, array $option = array(), $cache = false, $lifetime = Cache::EXPIRE_DEFAULT_TIME)
    {
        if ($cache) {
            $mobile = $this->browser->isMobile();
            $benchmark = Profiler::start('View', 'action (cached): ' . $strClass . ' mobile=' . $mobile);
            $cache_key = $this->generatorKey($strClass . ' mobile=' . $mobile, $option);
            if (($html = $this->cache->load($cache_key)) === NULL) {
                try {
                    $controllerFactory = $this->container->getService('controllerFactory');
                    $controllerFactory->setSuffixAction(IController::SUFFIX_PARTIAL);
                    $controller = $controllerFactory->createController($strClass);
                    $controller->isViewPartial = true;
                    $controller->execute($controllerFactory->getControllerMethod($strClass), array(), $option);
                    $html = (string)$controller->getResponse();
                    $this->cache->save($cache_key, $html, array(
                        Cache::EXPIRE => $lifetime
                    ));
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }
            if (isset($benchmark)) {
                Profiler::stop($benchmark);
            }
            return $html;
        }


        try {
            $benchmark = Profiler::start('View', 'action: ' . $strClass);
            $controllerFactory = $this->container->getService('controllerFactory');
            $controllerFactory->setSuffixAction(IController::SUFFIX_PARTIAL);
            $controller = $controllerFactory->createController($strClass);
            $controller->isViewPartial = true;
            $controller->execute($controllerFactory->getControllerMethod($strClass), array(), $option);
            if (isset($benchmark)) {
                Profiler::stop($benchmark);
            }
            return (string)$controller->getResponse();
        } catch (\Exception $e) {
            if (isset($benchmark)) {
                Profiler::stop($benchmark);
            }
            return $e->getMessage();
        }
    }

    /**
     * @param string $str
     * @return string
     */
    protected function escape($str, $ENT_QUOTES = false)
    {
        if (!is_string($str))
            return $str;

        return Strings::escape($str, $ENT_QUOTES);
    }

    /**
     * @param $template
     * @param $isAbsolute
     * @return bool|string
     */
    protected function hasTemplate($template, $isAbsolute)
    {
        try {
            $tpl = $this->getSelectedPath($template, $isAbsolute);
            return $tpl;
        } catch (Error $e) {
            $this->container->getService('logger')->error($e->getMessage(), 'view');
            return false;
        }
    }

    /**
     * @param $strClass
     * @param $option
     * @return string
     */
    protected function generatorKey($strClass, $option)
    {
        array_multisort($option);
        return _sf('object_{0}:action:html_{1}', md5(strtolower($strClass)), md5(json_encode($option)));
    }

    /**
     * Подключения шаблона
     * @param $_templet
     * @param array $_attr
     * @param bool $isAbsolute
     * @return string
     */
    public function partial($template, array $_attr = null, $isAbsolute = false)
    {
        return $this->load($template, $_attr, $isAbsolute);
    }


    /**
     * Путь до темы
     * @return string
     */
    protected function getPath()
    {
        if (
            $this->site->mobile &&
            $this->browser->isMobile() &&
            !$this->browser->isFullVersion()
        ) {
            $path = $this->site->template_path . '/' . $this->site->mobile;
        } else {
            $path = $this->site->template_path . '/' . $this->site->template;
        }

        $dir = realpath($path);
        if ($dir === FALSE) {
            return $this->getDefaultPath();
        } else {
            return $dir;
        }
    }

    /*
     * @throw Error
     */
    protected function getDefaultPath()
    {
        if (
            $this->site->mobile &&
            $this->browser->isMobile() &&
            !$this->browser->isFullVersion()
        ) {
            $path = $this->site->template_path . '/default_mobile';
        } else {
            $path = $this->site->template_path . '/default';
        }

        $dir = realpath($path);
        if ($dir === FALSE)
            throw new Error('Directory ' . $path . ' not found.');
        return $dir;
    }

    /*
     * @throw Error
     */
    protected function getSelectedPath($template, $isAbsolute)
    {
        if ($isAbsolute) {
            $_tpl = realpath($template . '.php');
            if (!file_exists($_tpl)) {
                throw new Error ('Not found tpl: ' . $_tpl);
            }
        } else {
            $_tpl = $this->getPath() . '/' . $template . '.php';
            if (!file_exists($_tpl)) {
                $_tpl = $this->getDefaultPath() . '/' . $template . '.php';
                if (!file_exists($_tpl)) {
                    throw new Error ('Not found tpl: ' . $this->getPath() . '/' . $template);
                }
            }
        }
        return $_tpl;
    }


}

