<?php
namespace CMS\Core\Helper;

use Delorius\Core\Environment;
use Delorius\Http\Url;
use Delorius\Utils\Strings;
use Delorius\View\Html;

class Visitor
{
    public static function register()
    {
        $container = Environment::getContext();
        /** @var \Delorius\Http\Url $url */
        $url = $container->getService('url');
        $query = $url->getQuery();
        $pos = strpos($query, 'utm_');
        if ($pos !== false) {
            $session = $container->getService('session');
            $referer = $session->getSection('referer');

            $referer->date = time();
            $referer->query = $query;
            $referer->utm = true;
            $referer->browser = (string)get(new \Browser());

        } elseif (isset($_SERVER['HTTP_REFERER'])) {
            $url = new \Delorius\Http\Url($_SERVER['HTTP_REFERER']);
            if ($container->getService('url')->getHost() != $url->getHost()) {
                $session = $container->getService('session');
                $referer = $session->getSection('referer');

                $referer->date = time();
                $referer->url = $url;
                $referer->utm = false;
                $referer->browser = (string)get(new \Browser());
            }

        }


    }

    public static function info()
    {

        $session = Environment::getContext()->getService('session');
        $referer = $session->getSection('referer');

        $date = $referer->date;

        if ($date > 0) {

            $html = '<br /><br />=======================================<p><b>Параметры визита:</b></p>';
            $html .= _sf('<p>Дата визита регистрации: {0}</p>', date('d.m.Y H:i', $referer->date));
            if ($referer->utm) {
                $html .= '<p>Параметры:</p>';
                parse_str($referer->query, $params);
                if (count($params))
                    foreach ($params as $name => $value) {
                        $html .= _sf('<p><b>{0}</b> = {1}</p>', Html::clearTags($name), Html::clearTags($value));
                    }
            } else {
                $url = $referer->url;
                if ($url instanceof Url) {
                    $html .= _sf('<p>Хост: {0}</p>', $url->getHost());
                    $html .= _sf('<p>Реферальная ссылка: {0}</p>', $url);
                }
            }

            $html .= '<br/>' . $referer->browser;

            return $html;
        }
    }

} 