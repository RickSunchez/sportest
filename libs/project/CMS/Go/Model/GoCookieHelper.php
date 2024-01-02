<?php
namespace CMS\Go\Model;

use Delorius\Core\Environment;
use Delorius\Core\Object;

class GoCookieHelper extends Object
{
    const NAME = 'go_refer';

    public static function SetCookie($goId,$isMail = false)
    {
        /** @var \Delorius\Http\IResponse $httpResponse */
        $httpResponse = Environment::getContext()->getService('httpResponse');
        $httpResponse->setCookie(self::NAME,self::encode($goId,$isMail),'+ 1 year','/',getDomainCookie());
    }

    /**
     * list($goId,$isMail) = GoCookieHelper::GetCookie();
     * @return array
     */
    public static function GetCookie(){
        /** @var \Delorius\Http\Request $httpRequest */
        $httpRequest = Environment::getContext()->getService('httpRequest');
        $cookie = $httpRequest->getCookie(self::NAME,false);
        if(!$cookie)
             return array();
        return self::decode($cookie);

    }

    protected static function encode($goId,$isMail = false){
        return "$goId|$isMail";
    }

    protected static function decode($string){
        return explode('|',$string);
    }
} 