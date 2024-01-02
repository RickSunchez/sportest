<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ahalyapov
 * Date: 15.07.13
 * Time: 20:47
 * To change this template use File | Settings | File Templates.
 */

namespace Delorius\Http\Curl\Wrapper;


use Delorius\Http\Curl\ClientCurl;

class Cookie {

    protected $cookie = array();
    protected $isSaveSession = false;

    public function __construct(ClientCurl $curl){
        $curl->onExecute[] = callback($this,'send');
        $curl->onExecuted[] = callback($this,'save');
    }

    public function set($name,$value = null){

        if(is_array($name)){
            $this->cookie += $name;
        }else{
            $this->cookie[$name] = $value;
        }
    }

    public function get($name = null){
        if ($name == null)
            return $this->cookie;
        else
            return $this->cookie[$name];
    }


    public function send(ClientCurl $curl){
        $curl->setOption(CURLOPT_COOKIE,$this->getStringCookie());

        if($this->isSaveSession){
            $curl->setOption(CURLOPT_HEADER,1);
        }

        $this->clean();
    }

    public function saveSession(){
        $this->isSaveSession = true;
    }

    public function save(ClientCurl $curl){
        if($this->isSaveSession){
            $size_header = $curl->getInfo('header_size');
            $header = substr($curl->getResponse(),0,$size_header);
            $this->parserCookieHeader($header);
            $body = substr($curl->getResponse(),$size_header);
            $curl->setResponse($body);
        }
    }

    public function clean(){
        $this->cookie = array();
    }

    protected function parserCookieHeader($header){
        preg_match_all("/Set-Cookie: (.*?)=(.*?);/i",$header,$res);
        foreach ($res[1] as $key => $value) {
            $this->set($value,$res[2][$key]);
        };
    }

    protected function getStringCookie(){
        $cookie = '';
        foreach($this->cookie as $name=>$value){
            $cookie .= "$name=$value; ";
        }
        return $cookie;
    }

}