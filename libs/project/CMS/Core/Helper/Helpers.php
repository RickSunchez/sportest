<?php
namespace CMS\Core\Helper;

use CMS\Core\Entity\Table;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Exception\Error;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Exception\NotFound;
use Delorius\Http\IResponse;
use Delorius\Http\Url;
use Delorius\Routing\RouterParameters;
use Delorius\Utils\Strings;

class Helpers
{
    protected static $_tables = array();

    /**
     * @param mixed $table_name
     * @param bool $error
     * @return int|null ID table object
     * @throws Error
     */
    public static function getTableId($table_name, $error = true)
    {

        if ($table_name instanceof ORM) {
            $table_name = $table_name->table_name();
        } elseif (!is_scalar($table_name)) {
            throw new Error('неверный идентификатор для таблицы');
        }

        if (!count(self::$_tables)) {
            $tables = Table::model()->select()->sort()->find_all();
            foreach ($tables as $table) {
                self::$_tables[$table['target_type']] = $table['id'];
            }
        }

        if (!isset(self::$_tables[$table_name]) && $error) {
            throw new Error('Не зарегистрирована таблица = ' . $table_name);
        }

        return self::$_tables[$table_name];
    }

    /**
     * @param string $path
     * @param bool|string $host
     * @return string
     */
    public static function canonicalUrl($path, $host = false)
    {
        $parser = parse_url($path);

        if (isset($parser['host'])) {
            return $path;
        }
        if (!$host) {
            /** @var Url $url */
            $url = Environment::getContext()->getService('url');
            return $url->getHostUrl() . $path;
        } else {
            $domainRouter = Environment::getContext()
                ->getService('domainRouter');
            $host = $domainRouter->generate($host);
            return $host . $path;
        }
    }

    /**
     * @return array
     * @throws \Delorius\Exception\Error
     */
    public static function getDomains()
    {
        $domains = Environment::getContext()->getParameters('domain');
        $list = Environment::getContext()->getParameters('cms.domain.show');
        $arr = array();
        if (count($list))
            foreach ($list as $router) {
                if (isset($domains[$router])) {
                    $arr[] = array('name' => $router, 'host' => $domains[$router][0]);
                }

            }
        return $arr;
    }

    /**
     * @return bool
     * @throws \Delorius\Exception\Error
     */
    public static function isMultiDomain()
    {
        return Environment::getContext()->getParameters('cms.domain.multi') ? true : false;
    }

    /**
     * Имя домани
     * @return mixed
     * @throws \Delorius\Exception\Error
     */
    public static function getCurrentDomain()
    {
        return Environment::getContext()->getService('site')->getParameters('domain._route');
    }

    /**
     * Проверка опций роутера
     * @param RouterParameters $paramsRouter
     */
    public static function checkOptionsRouter(RouterParameters $paramsRouter)
    {
        $opts = $paramsRouter->getOptions();


        #sort get params ?b=1&a=1 => ?a=1&b=1
        $url = Environment::getContext()->getService('http.url');
        if(PHP_SAPI !== "cli" &&
            strcmp($url->getBasePath().$url->getRelativeUrl(),$_SERVER["REQUEST_URI"]) != 0 &&
            !$opts['no_sort_get_params']
        ){
            $httpResponse = Environment::getContext()->getService('http.response');
            $httpResponse->redirect(
                $url,
                \Delorius\Http\Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        #Exception
        if ($opts['error'] == '404') {
            throw new NotFound();
        }
        if ($opts['error'] == '403') {
            throw new ForbiddenAccess();
        }

        #redirect
        if (isset($opts['redirect']) && !empty($opts['redirect'])) {

            $params = $paramsRouter->getParams();
            if (count($params)) {
                foreach ($params as $name => $value) {
                    $pattern['#\{' . $name . '\}#'] = $value;
                }
                $opts['redirect'] = Strings::replace($opts['redirect'], $pattern);
            }

            //router = false
            if (isset($opts['router']) && $opts['router'] === false) {
                Environment::getContext()->getService('httpResponse')->redirect(
                    $opts['redirect'],
                    $opts['code'] ? $opts['code'] : IResponse::S301_MOVED_PERMANENTLY
                );
                die;
            }

            list($path, $query) = explode('?', $opts['redirect']);
            if ($query) {
                parse_str($query, $out);
            }
            $redirect = link_to($path, (array)$out);

            Environment::getContext()->getService('httpResponse')->redirect(
                $redirect,
                $opts['code'] ? $opts['code'] : IResponse::S301_MOVED_PERMANENTLY
            );
            die;

        }

        if (isset($opts['code']) && !empty($opts['code'])) {
            Environment::getContext()->getService('httpResponse')->setCode($opts['code']);
        }

    }

}