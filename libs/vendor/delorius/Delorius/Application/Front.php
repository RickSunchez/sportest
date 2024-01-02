<?php
namespace Delorius\Application;

use Delorius\Configure\Site;
use Delorius\Core\Object;
use Delorius\Exception\Error;
use Delorius\Exception\EndExecuteController;
use Delorius\Exception\Gone;
use Delorius\Http\IRequest;
use Delorius\Http\IResponse;
use Delorius\Routing\Router;
use Delorius\Routing\RouterParameters;
use Delorius\Tools\Debug\Profiler;
use Delorius\Utils\Json;
use Delorius\View\Html;
use Delorius\View\View;

class Front extends Object
{
    /** @var array of function(Front $sender); Occurs before the application loads presenter */
    public $onStartup;

    /** @var array of function(Front $sender, \Exception $e = NULL); Occurs before the application shuts down */
    public $onShutdown;

    /** @var array of function(Front $sender, array $paramsRouter); Occurs when a new request is received */
    public $onRequest;

    /** @var array of function(Front $sender, string|array|\Delorius\Utils\Html $response); Occurs when a new response is ready for dispatch */
    public $onResponseBefore;
    public $onResponseAfter;

    /** @var array of function(Front $sender, \Exception $e); Occurs when an unhandled exception occurs in the application */
    public $onError;

    /** @var \Delorius\View\View */
    public $view;

    /** @var bool */
    public $ajaxMode;

    /** @var \Delorius\Configure\Site */
    public $site;

    /** @var string|array|Html */
    public $response;

    /** @var bool enable fault barrier? */
    public $catchExceptions = true;

    /** @var \Delorius\Http\IRequest */
    public $httpRequest;

    /** @var \Delorius\Http\IResponse */
    public $httpResponse;

    /** @var \Delorius\Routing\Router */
    public $router;

    /** @var \Delorius\Application\IControllerFactory */
    public $controllerFactory;

    public function __construct(
        Router $router,
        IRequest $httpRequest,
        IResponse $httpResponse,
        Site $site,
        IControllerFactory $controllerFactory
    )
    {
        $this->router = $router;
        $this->httpRequest = $httpRequest;
        $this->httpResponse = $httpResponse;
        $this->site = $site;
        $this->controllerFactory = $controllerFactory;
        $this->ajaxMode = $this->httpRequest->isAjax();
        $this->view = new View();
    }

    public function run()
    {
        try {

            $benchmark = Profiler::start('Front', 'run');

            $this->onStartup($this);
            $this->processRequest($this->createInitialRequest());
            $this->onShutdown($this);

            if (isset($benchmark)) {
                Profiler::stop($benchmark);
            }

        } catch (\Exception $e) {
            if (isset($benchmark)) {
                Profiler::delete($benchmark);
            }
            $this->onError($this, $e);
            if ($this->catchExceptions) {
                try {
                    $this->processException($e);
                    $this->onShutdown($this, $e);
                    return;
                } catch (\Exception $e) {
                    $this->onError($this, $e);
                }
            }
            $this->onShutdown($this);
            throw $e;
        }
    }

    /**
     * @param string|Html $response
     */
    public function response($response)
    {
        $this->response = $response;
    }

    /**
     * @return RouterParameters|null
     * @throws NotFound
     */
    public function createInitialRequest()
    {
        $paramsRouter = $this->router->getControllerParams($this->httpRequest);
        if (!$paramsRouter) {
            throw new Gone('No route for HTTP request: ' . $this->httpRequest->getUrl());
        }
        $this->site->router = $paramsRouter->getName();
        $this->site->controller = $paramsRouter->getController();
        $this->site->router_params = $paramsRouter->getParams();

        $this->onRequest($this, $paramsRouter);

        return $paramsRouter;
    }

    /**
     * @return void
     */
    public function processRequest(RouterParameters $paramsRouter)
    {
        try {
            $this->controllerFactory->setSuffixAction(IController::SUFFIX_ACTION);
            $controller = $this->controllerFactory->createController($paramsRouter->getController());
            $controller->execute($this->controllerFactory->getControllerMethod($paramsRouter->getController()), $paramsRouter->getParams());
            $this->response($controller->getResponse());
        } catch (EndExecuteController $e) {
            $this->response($e->getResponse());
        }

        $this->onResponseBefore($this, $this->response);

        if (!$this->ajaxMode && $this->site->layout != null && !is_array($this->response)) {
            $this->response($this->view->load($this->site->layout, array('response' => $this->response)));
        }

        $this->onResponseAfter($this, $this->response);

        $is_array = false;
        if (is_array($this->response)) {
            $is_array = true;
            $this->response(Json::encode($this->response));
        }

        # если это ajax запрос и виде массива
        if ($this->ajaxMode || $is_array) {
            $this->httpResponse->setContentType('application/json', 'UTF-8');
        }

        echo $this->response;

    }

    /**
     * @return void
     */
    public function processException(\Exception $e)
    {
        if (!$this->httpResponse->isSent() && $e instanceof Error) {
            $code = $e->getCode();
        } else {
            $code = 500;
        }
        $this->httpResponse->setCode($code);
        $this->processRequest($this->router->match('/' . $code . '.error'));
    }


}