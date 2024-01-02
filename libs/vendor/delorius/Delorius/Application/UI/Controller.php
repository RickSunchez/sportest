<?php

namespace Delorius\Application\UI;

use Delorius\Application\IController;
use Delorius\Core\Environment;
use Delorius\Exception\BadRequest;
use Delorius\Exception\EndExecuteController;
use Delorius\Exception\Error;
use Delorius\Utils\Arrays;
use Delorius\View\Html;
use Delorius\View\View;

class Controller extends Control implements IController
{
    /** @var array router params */
    private static $_router = array();

    /** @var View */
    protected $view;

    /** @var bool */
    protected $end = false;

    /** @var array */
    private $globalParams = array();

    /**
     * @var \Delorius\Application\SignalReceiver
     * @service signalReceiver
     * @inject
     */
    public $serviceSignalReceiver;

    /**
     * @var string
     */
    public $signalReceiver;

    /** @var string */
    public $signal;

    /** @var \Delorius\DI\Container */
    public $container;

    /**
     * @var \Delorius\Http\Context
     * @service httpContext
     * @inject
     */
    public $httpContext;

    /**
     * @var \Delorius\Http\IRequest
     * @service httpRequest
     * @inject
     */
    public $httpRequest;

    /**
     * @var \Delorius\Http\IResponse
     * @service httpResponse
     * @inject
     */
    public $httpResponse;

    /**
     * @var \Delorius\Http\UrlScript
     * @service url
     * @inject
     */
    public $urlScript;

    /** @var \Delorius\Http\Session
     * @service session
     * @inject
     */
    public $session;

    /**
     * @var \Delorius\Security\User
     * @service user
     * @inject
     */
    public $user;

    /**
     * @var \Delorius\Application\IControllerFactory
     * @service controllerFactory
     * @inject
     */
    public $controllerFactory;

    /**
     * @var \Delorius\Configure\Site
     * @service site
     * @inject
     */
    public $site;

    /**
     * @var string|array|Html
     */
    public $response;

    /** @var array of function(Controller $sender); Occurs before the application loads presenter */
    public $onStartup;

    /** @var array of function(Controller $sender, \Exception $e = NULL); Occurs before the application shuts down */
    public $onShutdown;

    /** @var array of function(Controller $sender, string|array|\Delorius\Utils\Html $response); Occurs when a new response is ready for dispatch */
    public $onResponse;

    /** @var array of function(Controller $sender, \Exception $e); Occurs when an unhandled exception occurs in the application */
    public $onError;


    /**
     * @param array|Html|string $response
     */
    public function response($response)
    {
        $this->response = $response;
    }

    /**
     * @return array|Html|string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns a list of behaviors that this model should behave as.
     * The return value should be an array of behavior configurations indexed by
     * behavior names. Each behavior configuration can be either a string specifying
     * the behavior class or an array of the following structure:
     * <pre>
     * 'behaviorName'=>array(
     *     'class'=>'\path\to\BehaviorClass',
     *     'property1'=>'value1',
     *     'property2'=>'value2',
     * )
     * or
     * behaviorName'=> '\path\to\BehaviorClass' ||  new \path\to\BehaviorClass()
     * </pre>
     *
     * Note, the behavior classes must implement {@link IBehavior} or extend from
     * {@link Behavior}. Behaviors declared in this method will be attached
     * to the model when it is instantiated.
     * @return array the behavior configurations (behavior name=>behavior configuration)
     */
    protected function behaviors()
    {
        return array();
    }


    /**
     * Automatically executed before the controller action. Can be used to set
     * class properties, do authorization checks, and execute other custom code.
     *
     * @return  void
     */
    public function before()
    {
        // Nothing by default
    }

    /**
     * Automatically executed after the controller action. Can be used to apply
     * transformation to the response, add extra output, and execute
     * other custom code.
     *
     * @return  void
     */
    public function after()
    {
        // Nothing by default
    }

    protected function init()
    {
        // Nothing by default
    }


    /**
     * Returns pair signal receiver and name.
     * @return array|NULL
     */
    final public function getSignal()
    {
        return $this->signal === NULL ? NULL : array($this->signalReceiver, $this->signal);
    }


    /**
     * Checks if the signal receiver is the given one.
     * @param  mixed $component or its id
     * @param  string $signal name (optional)
     * @return bool
     */
    final public function isSignalReceiver($component, $signal = NULL)
    {
        if ($component instanceof \Delorius\ComponentModel\Component) {
            $component = $component === $this ? '' : $component->lookupPath(__CLASS__, TRUE);
        }

        if ($this->signal === NULL) {
            return FALSE;

        } elseif ($signal === TRUE) {
            return $component === ''
                || strncmp($this->signalReceiver . '-', $component . '-', strlen($component) + 1) === 0;

        } elseif ($signal === NULL) {
            return $this->signalReceiver === $component;

        } else {
            return $this->signalReceiver === $component && strcasecmp($signal, $this->signal) === 0;
        }
    }

    /**
     * @return void
     * @throws Error
     */
    public function processSignal()
    {
        if ($this->signal === NULL) {
            return;
        }

        try {
            $component = $this->signalReceiver === '' ? $this : $this->getComponent($this->signalReceiver, FALSE);
        } catch (Error $e) {
        }

        if (isset($e) || $component === NULL) {
            return;

        } elseif (!$component instanceof ISignalReceiver) {
            throw new BadRequest("The signal receiver component '$this->signalReceiver' is not ISignalReceiver implementor.");
        }

        if ($component instanceof IStateControl)
            $component->loadState((array)$this->globalParams[$this->signalReceiver]);

        $component->signalReceived($this->signal);
        if ($this->httpRequest->isAjax()) {
            $params = array();
            $component->saveState($params);
            $this->response(array('snippet' => (string)$component, 'status' => $params));
            $this->endProgram(true);
        }
        $this->signal = NULL;
    }

    /**
     * Initializes $this->globalParams, $this->signal & $this->signalReceiver
     * @return void
     */
    private function initGlobalParameters()
    {
        $this->container = Environment::getContext();
        $this->view = new View($this);
        $this->attachBehaviors($this->behaviors());
        $this->globalParams = array();
        $this->globalParams = $this->serviceSignalReceiver->globalParams();
        $this->signalReceiver = $this->serviceSignalReceiver->getSignalReceiver();
        $this->signal = $this->serviceSignalReceiver->getSignal();
        $this->loadState($this->serviceSignalReceiver->getParams());
    }


    public function forward($class_name, array $params = null, $suffix = IController::SUFFIX_ACTION)
    {
        try {
            $this->controllerFactory->setSuffixAction($suffix);
            $controller = $this->controllerFactory->createController($class_name);
            $controller->execute($this->controllerFactory->getControllerMethod($class_name), array(), (array)$params);
            $this->response($controller->getResponse());
        } catch (\Exception $e) {
            throw $e;
        }

        /* если требует остановка выполения контролеров */
        if ($this->end)
            $this->endProgram(true);
    }

    public function endProgram($isNow = false)
    {
        if ($isNow)
            throw new EndExecuteController('Controller stop: ' . get_called_class(), $this->response);
        $this->end = true;
    }

    /**
     * @param null $name
     * @return array
     */
    public function getRouter($name = null)
    {
        if ($name == null)
            return self::$_router;
        else
            return self::$_router[$name];
    }

    private function getMergeRouterAndQuery()
    {
        return Arrays::mergeTree(
            self::$_router,
            $this->httpRequest->getQuery()
        );
    }

    /**
     * @param mixed $params
     * @param null $value
     */
    public function setRouter($params, $value = null)
    {

        if (is_array($params) && count($params)) {
            if (isset($params['_domain'])) {
                $domain = $params['_domain'];
                unset($params['_domain']);
                $params += $domain;
            }
            self::$_router = $params;
        } elseif (is_scalar($params)) {
            self::$_router[$params] = $value;
        }
    }

    /********************* interface IController ********************/

    /** @var bool view->action(?) = true */
    public $isViewPartial = false;

    /**
     * @param string $method
     * @param array|null $params
     * @param array $options
     * @throws BadRequest
     * @throws Error
     */
    public function execute($method, array $params = null, array $options = array())
    {
        $this->setRouter($params);
        $this->initGlobalParameters();
        $this->init();
        $this->onStartup($this);
        $this->before();
        $this->processSignal();
        $result = $this->tryCall($method, $this->getMergeRouterAndQuery() + $options);
        if (!$result)
            throw new Error('Not set method ' . $method);
        $this->onResponse($this, $this->getResponse());
        $this->after();
        $this->onShutdown($this);
    }


    /**
     * Attempts to cache the sent entity by its last modification date.
     * @param  string|int|\DateTime  last modified time
     * @param  string strong entity tag validator
     * @param  mixed  optional expiration time
     * @return void
     * @throws EndExecuteController
     */
    public function lastModified($lastModified, $etag = NULL, $expire = NULL)
    {
        if ($expire !== NULL) {
            $this->httpResponse->setExpiration($expire);
        } else {
            $seconds = Environment::getContext()->getService('config')->get('http.expire');
            if ($seconds) {
                $time = time();
                $expire = $time + $seconds;
                $this->httpResponse->setExpiration($expire);
            }
        }

        if (!$this->httpContext->isModified($lastModified, $etag)) {
            $this->endProgram(true);
        }
    }

}