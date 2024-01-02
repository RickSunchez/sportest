<?php
namespace Delorius\Application;

use Delorius\Core\Object;
use Delorius\Exception\BadRequest;
use Delorius\Http\IRequest;

class SignalReceiver extends Object {

    const SIGNAL_KEY = 'do';

    /** @var array */
    private $globalParams = array();

    /** @var array */
    private $params = array();

    /** @var string */
    private $signalReceiver;

    /** @var string */
    private $signal;

    /** @var \Delorius\Http\IRequest */
    private $httpRequest;

    public function __construct(IRequest $httpRequest){
        $this->httpRequest = $httpRequest;
        $this->init();
    }

    protected function init(){

        $params = $this->httpRequest->getQuery();
        if($this->httpRequest->isAjax() || $this->httpRequest->getPost(self::SIGNAL_KEY) ){
            $params += $this->httpRequest->getPost();
        }


        foreach ($params as $key => $value) {
            if (!preg_match('#^((?:[a-z0-9_]+-)*)((?!\d+\z)[a-z0-9_]+)\z#i', $key, $matches)) {
                continue;
            } elseif (!$matches[1]) {
                $selfParams[$key] = $value;
            } else {
                $this->globalParams[substr($matches[1], 0, -1)][$matches[2]] = $value;
            }
        }

        if (isset($selfParams[self::SIGNAL_KEY])) {
            $param = $selfParams[self::SIGNAL_KEY];
            if (!is_string($param)) {
                throw new BadRequest ('Signal name is not string.');
            }
            $pos = strrpos($param, '-');
            if ($pos) {
                $this->signalReceiver = substr($param, 0, $pos);
                $this->signal = substr($param, $pos + 1);
            } else {
                $this->signalReceiver = '';
                $this->signal = $param;
            }
            if ($this->signal == NULL) { // intentionally ==
                $this->signal = NULL;
            }

            unset($selfParams[self::SIGNAL_KEY]);
        }

        $this->params = $selfParams;
    }

    public function globalParams(){
        return (array) $this->globalParams;
    }

    public function getParams(){
        return (array) $this->params;
    }

    public function getSignal(){
        return $this->signal ? $this->signal : null ;
    }

    public function getSignalReceiver(){
        return $this->signalReceiver ? $this->signalReceiver : null;
    }







}