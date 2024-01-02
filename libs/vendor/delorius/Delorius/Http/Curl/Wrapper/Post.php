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

class Post
{

    protected $post = array();

    public function __construct(ClientCurl $curl)
    {
        $curl->onExecute[] = callback($this, 'send');
    }

    public function set($name, $value = null)
    {

        if (is_array($name)) {
            $this->post += $name;
        } else {
            $this->post[$name] = $value;
        }
    }

    /** Если был указан необходимоть отсылки пост параметров */
    public function send(ClientCurl $curl)
    {
        $curl->setOption(CURLOPT_POST, 1);
        $curl->setOption(CURLOPT_POSTFIELDS, $this->getPostArray());
        $this->clean();
    }

    public function clean()
    {
        $this->post = array();
    }

    private function getPostArray()
    {
        return http_build_query($this->post);
    }

}