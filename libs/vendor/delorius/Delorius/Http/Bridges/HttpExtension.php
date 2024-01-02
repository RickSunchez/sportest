<?php
namespace Delorius\Http\Bridges;

use Delorius\DI\CompilerExtension;

/**
 * HTTP extension for Delorius DI.
 */
class HttpExtension extends CompilerExtension
{
    public $defaults = array(
        'proxy' => array(),
        'headers' => array(
            'X-Powered-By' => 'Delorius 2.0 Framework',
            'Content-Type' => 'text/html; charset=utf-8',
        ),
        'frames' => 'SAMEORIGIN', // X-Frame-Options
    );


    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $container->addDefinition($this->prefix('requestFactory'))
            ->setClass('Delorius\Http\RequestFactory')
            ->addSetup('setProxy', array($config['proxy']));

        $container->addDefinition($this->prefix('request'))
            ->setClass('Delorius\Http\IRequest')
            ->setFactory('@Delorius\Http\RequestFactory::createHttpRequest');

        $container->addDefinition($this->prefix('response'))
            ->setClass('Delorius\Http\IResponse')
            ->setFactory('Delorius\Http\Response');

        $container->addDefinition($this->prefix('context'))
            ->setClass('Delorius\Http\Context');

        $container->addDefinition($this->prefix('url'))
            ->setClass('Delorius\Http\UrlScript')
            ->setFactory('@Delorius\Http\IRequest::getUrl');

        if ($this->name === 'http') {
            $container->addAlias('httpRequestFactory', $this->prefix('requestFactory'));
            $container->addAlias('httpContext', $this->prefix('context'));
            $container->addAlias('httpRequest', $this->prefix('request'));
            $container->addAlias('httpResponse', $this->prefix('response'));
            $container->addAlias('url', $this->prefix('url'));
            $container->addAlias('urlScript', $this->prefix('url'));
        }

    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->getConfig();

        if (isset($config['frames']) && $config['frames'] !== TRUE) {

            $initialize->addBody('
            $frames = ?;
            if ($frames === FALSE) {
                $frames = "DENY";
            } elseif (is_scalar($frames) && is_int(strpos($_SERVER["HTTP_REFERER"], $frames)) === true) {
                $frames = "ALLOW-ALL";
            } elseif (count($frames)) {
                $isset = false;
                foreach ($frames as $domain) {
                    if (is_int(strpos($_SERVER["HTTP_REFERER"], $domain)) === true) {
                        $frames = "ALLOW-ALL";
                        $isset = true;
                        break;
                    }
                }
                if($isset == false)
                    $frames = "SAMEORIGIN";

            } else {
                $frames = "SAMEORIGIN";
            }

            header("X-Frame-Options: $frames");
            ', array($config['frames']));
        }

        foreach ($config['headers'] as $key => $value) {
            if ($value != NULL) { // intentionally ==
                $initialize->addBody('header(?);', array("$key: $value"));
            }
        }
    }

}
