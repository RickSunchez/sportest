<?php
namespace Delorius\Http\Curl\Wrapper;


use Delorius\Http\Curl\ClientCurl;

class Header {

    protected $headers = array();

    public function __construct(ClientCurl $curl){
        $curl->onExecute[] = callback($this,'send');
    }

    public function set($header){

        if(is_array($header)){
            $this->headers = $header;
        }else{
            $this->headers[] = $header;
        }
    }

    public function send(ClientCurl $curl){
        $curl->setOption(CURLOPT_HTTPHEADER,$this->headers);
        $this->clean();
    }

    public function clean(){
        $this->headers = array();
    }

}