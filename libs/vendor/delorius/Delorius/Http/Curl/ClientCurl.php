<?php
namespace Delorius\Http\Curl;

use Delorius\Core\Object;
use Delorius\Http\Curl\Wrapper\Cookie;
use Delorius\Http\Curl\Wrapper\Header;
use Delorius\Http\Curl\Wrapper\Post;

if (!function_exists('curl_init')) {
    throw new \Exception('Needs the CURL PHP extension.');
}

class ClientCurl extends Object {

    const DEFAUL_AGETN = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9';

    /** @var array of function(ClientCurl $curl); Запускается перед выполнением запроса */
    public $onExecute;

    /** @var array of function(ClientCurl $curl); Запускается после выполнением запроса */
    public $onExecuted;

    /** @var \Delorius\Http\Curl\Wrapper\Post  */
    private $_post;

    /** @var \Delorius\Http\Curl\Wrapper\Cookie  */
    private $_cookie;

    /** @return \Delorius\Http\Curl\Wrapper\Header */
    private $_header;

    protected $error;
    protected $info;
    protected $result;

    /** @var resource  curl_init */
    protected $ch;

    /** @var array Начальные опции Curl */
    protected $opts= array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true, // не вывосдить сразу на экран
        CURLOPT_TIMEOUT => 30,
    );


    public function __construct($url = null){
        $this->ch = curl_init();
        if($url != null)
            $this->setUrl($url);
    }

    public function exec($opts = array()){
        $this->onExecute($this);
        $curl_options = $opts + $this->opts;
        curl_setopt_array($this->ch,(array) $curl_options);
        $this->result = curl_exec($this->ch);
        $this->info = curl_getinfo($this->ch);
        $this->error = curl_error($this->ch);
        $this->onExecuted($this);
        return $this;
    }

    public function getResponse(){
        return $this->result;
    }

    public function setResponse($response){
        $this->result = $response;
        return $this;
    }

    public function setOption($name,$value){
        $this->opts[$name] =  $value;
        return $this;
    }

    public function setUrl($url){
        $this->setOption(CURLOPT_URL,$url);
        return $this;
    }

    public function setReferer($url){
        $this->setOption(CURLOPT_REFERER,$url);
        return $this;
    }

    public function  setFollow($isOk = 1){
        $this->setOption(CURLOPT_FOLLOWLOCATION,$isOk);
        return $this;
    }

    public function setUserAgent($agent = self::DEFAUL_AGETN){
        $this->setOption(CURLOPT_USERAGENT,$agent);
        return $this;
    }


    public function setPost($name,$value){
        $this->getWrapperPost()->set($name,$value);
        return $this;
    }

    public function setPosts(array $post){
        $this->getWrapperPost()->set($post);
        return $this;
    }

    public function setCookie($name,$value){
        $this->getWrapperCookie()->set($name,$value);
        return $this;
    }

    public function setCookies(array $cookie){
        $this->getWrapperCookie()->set($cookie);
        return $this;
    }

    public function getCookie($name = null){
        return $this->getWrapperCookie()->get($name);
    }

    public function saveSession(){
        $this->getWrapperCookie()->saveSession();
        return $this;
    }

    public function setHeader($header){
        $this->getWrapperHeader()->set($header);
        return $this;
    }

    public function setHeaders(array $headers){
        $this->getWrapperHeader()->set($headers);
        return $this;
    }

    public function setMethod($method){
        switch ($method) {
            case 'DELETE':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PUT':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
            case 'POST':
                curl_setopt($this->curl, CURLOPT_POST, true);
                break;
            default:
            case 'GET':
                curl_setopt($this->curl, CURLOPT_HTTPGET, true);
                break;
            case 'HEAD':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
                break;
        }
    }

    public function getInfo($name = null) {
        if ($name == null)
            return $this->info;
        else
            return $this->info[$name];
    }

    public function getError($name = null) {
        if ($name == null)
            return $this->error;
        else
            return $this->error[$name];
    }

    public function reset() {
        $this->info = null;
        $this->result = null;
        $this->error = null;
        return $this;
    }

    /** @return \Delorius\Http\Curl\Wrapper\Post */
    private function getWrapperPost(){
        if($this->_post == null){
            $this->_post = new Post($this);
        }
        return $this->_post;
    }

    /** @return \Delorius\Http\Curl\Wrapper\Cookie */
    private function getWrapperCookie(){
        if($this->_cookie == null){
            $this->_cookie = new Cookie($this);
        }
        return $this->_cookie;
    }

    /** @return \Delorius\Http\Curl\Wrapper\Header */
    private function getWrapperHeader(){
        if($this->_header == null){
            $this->_header = new Header($this);
        }
        return $this->_header;
    }

    public function __destruct(){
        curl_close($this->ch);
    }




}