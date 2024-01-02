<?php
namespace Delorius\Application\UI;

use Delorius\Application\SignalReceiver;
use Delorius\ComponentModel\Container;
use Delorius\Core\Environment;
use Delorius\Exception\BadRequest;
use Delorius\Exception\Error;

class Control extends Container implements \ArrayAccess, ISignalReceiver, IStateControl {

    /** @var array */
    protected $params = array();

    /**
     * Formats signal handler method name -> case sensitivity doesn't matter.
     * @param  string
     * @return string
     */
    public static function formatSignalMethod($signal)
    {
        return $signal == NULL ? NULL : 'handle' . $signal; // intentionally ==
    }


    /**
     * Calls public method if exists.
     * @param  string
     * @param  array
     * @return bool  does method exist?
     */
    public  function tryCall($method, array $params)
    {
        $rc = $this->getReflection();
        if ($rc->hasMethod($method)) {
            $rm = $rc->getMethod($method);
            if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
                $rm->invokeArgs($this, $rc->combineArgs($rm, $params));
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Access to reflection.
     * @return ControllerComponentReflection
     */
    public static function getReflection()
    {
        return new ControllerComponentReflection(get_called_class());
    }



    /********************* render control  *******************/

    public function render(){
        return 'need to implement the method '.get_called_class().'::"render" ';
    }

    function __toString(){
        return $this->render();
    }



    /********************* interface ISignalReceiver ****************d*g**/


    /**
     * Calls signal handler method.
     * @param  string
     * @return void
     * @throws Error if there is not handler method
     */
    public function signalReceived($signal)
    {
        if (!$this->tryCall($this->formatSignalMethod($signal), $this->params)) {
            $class = get_class($this);
            throw new BadRequest("There is no handler for signal '$signal' in class $class.");
        }
    }

    /********************* interface IStateControl ****************d*g**/



    /**
     * Loads state informations.
     * @param  array
     * @return void
     */
    public function loadState(array $params)
    {
        $reflection = $this->getReflection();
        foreach ($reflection->getPersistentParams() as $name => $meta) {
            if (isset($params[$name])) { // NULLs are ignored
                $type = gettype($meta['def']);
                if (!$reflection->convertType($params[$name], $type)) {
                    throw new Error("Invalid value for persistent parameter '$name' in '{$this->getName()}', expected " . ($type === 'NULL' ? 'scalar' : $type) . ".");
                }
                $this->$name = $params[$name];
            } else {
                $params[$name] = $this->$name;
            }
        }
        $this->params = $params;
    }

    /**
     * Saves state informations for next request.
     * @param  array
     * @return void
     */
    public function saveState(array & $params,$reflection = null)
    {
        $reflection = $reflection === NULL ? $this->getReflection() : $reflection;
        foreach ($reflection->getPersistentParams() as $name => $meta) {

            if (isset($params[$name])) {
                // injected value

            } elseif (array_key_exists($name, $params)) { // NULLs are skipped
                continue;

            } elseif (!isset($meta['since']) || $this instanceof $meta['since']) {
                $params[$name] = $this->$name; // object property value

            } else {
                continue; // ignored parameter
            }

            $type = gettype($meta['def'] === NULL ? $params[$name] : $meta['def']); // compatible with 2.0.x
            if (!ControllerComponentReflection::convertType($params[$name], $type)) {
                throw new Error("Invalid value for persistent parameter '$name' in '{$this->getName()}', expected " . ($type === 'NULL' ? 'scalar' : $type) . ".");
            }

            if ($params[$name] === $meta['def'] || ($meta['def'] === NULL && is_scalar($params[$name]) && (string) $params[$name] === '')) {
                $params[$name] = NULL; // value transmit is unnecessary
            }
        }

    }


    /**
     * Returns array of classes persistent parameters. They have public visibility and are non-static.
     * This default implementation detects persistent parameters by annotation @persistent.
     * @return array
     */
    public static function getPersistentParams()
    {
        $rc = new \Delorius\Reflection\ClassType(get_called_class());
        $params = array();
        foreach ($rc->getProperties(\ReflectionProperty::IS_PUBLIC) as $rp) {
            if (!$rp->isStatic() && $rp->hasAnnotation('persistent')) {
                $params[] = $rp->getName();
            }
        }
        return $params;
    }

    /********************* interface \ArrayAccess *******************/

    /**
     * Adds the component to the container.
     * @param  string $name component name
     * @param  \Delorius\ComponentModel\IComponent $component
     * @return void
     */
    final public function offsetSet($name, $component)
    {
        $this->addComponent($component, $name);
    }



    /**
     * Returns component specified by name. Throws exception if component doesn't exist.
     * @param  string $name component name
     * @return \Delorius\ComponentModel\IComponent
     * @throws Error
     */
    final public function offsetGet($name)
    {
        return $this->getComponent($name, TRUE);
    }



    /**
     * Does component specified by name exists?
     * @param  string $name component name
     * @return bool
     */
    final public function offsetExists($name)
    {
        return $this->getComponent($name, FALSE) !== NULL;
    }



    /**
     * Removes component from the container.
     * @param  string $name component name
     * @return void
     */
    final public function offsetUnset($name)
    {
        $component = $this->getComponent($name, FALSE);
        if ($component !== NULL) {
            $this->removeComponent($component);
        }
    }



    /**
     * Returns a fully-qualified name that uniquely identifies the component
     * within the presenter hierarchy.
     * @return string
     */
    public function getUniqueId()
    {
        return $this->lookupPath('Delorius\Application\UI\Controller', TRUE);
    }

    /**
     * Returns snippet HTML ID.
     * @param  string $name snippet name
     * @return string
     */
    public function getSnippetId($name = NULL)
    {
        // HTML 4 ID & NAME: [A-Za-z][A-Za-z0-9:_.-]*
        return 'snippet-' . $this->getUniqueId() . '-' . $name;
    }

    public function link($handler,array $params = null,$name_router = null,array $parameters = array(),$absoluteUrl = false){
        $selfParams = array();
        $selfParams[SignalReceiver::SIGNAL_KEY] = $this->getUniqueId().'-'.$handler;
        if(sizeof($params)){
            foreach($params as $name=>$value){
                $selfParams[$this->getUniqueId().'-'.$name] = $value;
            }
        }

        if($name_router === null){
            /** @var \Delorius\Http\Url $url */
            $url = Environment::getContext()->getService('httpRequest')->getUrl();
            $url->setQuery($selfParams);
            return $url->getAbsoluteUrl();
        }else{

            return
                Environment::getContext()->getService('router')->generate($name_router, $parameters, $absoluteUrl).
                '?'.
                http_build_query($selfParams, '', '&');
        }

    }





}