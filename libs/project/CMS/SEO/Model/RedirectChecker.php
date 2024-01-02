<?php

namespace CMS\SEO\Model;


use CMS\SEO\Entity\Redirect;
use Delorius\Core\Environment;
use Delorius\Http\IResponse;
use Delorius\Utils\Strings;

class RedirectChecker
{

    public static function check($path)
    {
        $instance = new self();
        $redirect = $instance->match($path);
        if (!$redirect) return;

        switch ($redirect['type_move']) {
            case Redirect::MOVE_PATH:
                $instance->redirect($redirect['move']);
                break;
            case Redirect::MOVE_ROUTER:
                $route = _sf($redirect['move'], $redirect['match']);
                list($path, $query) = explode('?', $route);
                parse_str($query, $out);
                $link = link_to($path, (array)$out);
                $instance->redirect($link);
                break;
            case Redirect::MOVE_CALLBACK:
                $instance->callback($redirect['move'], $redirect);
                break;
            default:
                return;
        }
    }

    /**
     * @param $path
     * @return bool|array
     */
    public function match($path)
    {
        $collections = $this->getCollections();
        foreach ($collections as $redirect) {

            if ($redirect['type_url'] == Redirect::PATH_URL) {
                if ($redirect['url'] == Strings::lower($path)) {
                    return $redirect;
                }
                continue;
            } elseif ($redirect['type_url'] == Redirect::PATH_TMP) {
                $result = $this->checkTemplate($path, $redirect['url']);
                if ($result) {
                    $redirect['match'] = $result;
                    return $redirect;
                }
                continue;
            }
        }

        return false;
    }

    /**
     * @param $path
     * @throws \Delorius\Exception\Error
     * @throws \Delorius\Exception\MissingService
     */
    public function redirect($path)
    {
        Environment::getContext()
            ->getService('httpResponse')
            ->redirect($path, IResponse::S301_MOVED_PERMANENTLY);
        exit();
    }

    /**
     * @param string $func Function|Class
     * @param string $path URL
     */
    public function callback($func, $path)
    {
        $arg = array('path' => $path);
        if (strpos($func, '::') === FALSE) {
            $function = new \ReflectionFunction($func);
            $function->invokeArgs($arg);
        } else {
            list($class, $method) = explode('::', $func, 2);
            $method = new \ReflectionMethod($class, $method);
            $method->invokeArgs(NULL, $arg);
        }
    }

    /**
     * @return \Delorius\Core\ORM|\Delorius\DataBase\Result
     * @throws \Delorius\Exception\Error
     */
    protected function getCollections()
    {
        $collections = Redirect::model()
            ->select()
            ->sort()
            ->cached()
            ->find_all();
        return $collections;
    }

    /**
     * @param $path
     * @param $tmp
     * @return bool|mixed
     */
    protected function checkTemplate($path, $tmp)
    {
        $match = Strings::match($path, '#' . $tmp . '#');
        if (!$match) return false;
        array_shift($match);
        return $match;
    }

}