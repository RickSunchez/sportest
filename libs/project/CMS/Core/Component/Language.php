<?php
namespace CMS\Core\Component;

use Delorius\Core\Common;
use Delorius\Core\Object;
use Delorius\Http\IRequest;

class Language extends Object
{
    /**
     * @var IRequest
     * @service httpRequest
     * @inject
     */
    public $httpRequest;

    public function __construct(IRequest $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    const NAME = 'lang';
    const DEFAULT_LANG = 'ru';


    protected $lang;
    protected $message = array();

    public function init()
    {
        if ($this->httpRequest->getQuery(self::NAME, null) !== null) {
            $this->set($this->httpRequest->getQuery(self::NAME));
        } elseif ($this->httpRequest->getCookie(self::NAME, null) !== null) {
            $this->set($this->httpRequest->getCookie(self::NAME));
        } else {
            $this->set(self::DEFAULT_LANG);
        }
    }

    public function set($lang)
    {
        $this->lang = $lang;
    }

    public function translate()
    {
        if (func_num_args() < 2) {
            return '';
        }
        $list = func_get_args();
        $class = array_shift($list);
        $name = array_shift($list);
        $str = $this->getMessage($class . ':' . $this->lang, $name);
        $args = array($str);
        foreach ($list as $value) {
            $args[] = $value;
        }
        return call_user_func_array('_sf', $args);
    }

    /**
     * @param $class string {Project}:{Bundle}:{Lang}
     */
    protected function getMessage($class, $name)
    {
        if (!isset($this->message[$class])) {
            $this->message[$class] = Common::getMessages($class);
        }
        return !empty($this->message[$class][$name]) ? $this->message[$class][$name] : '{' . $class . ':' . $name . '}';
    }
}
