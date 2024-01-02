<?php
namespace Delorius\Utils;

use Delorius\Exception\Error;
use Delorius\Core\Object;
use Delorius\Reflection\GlobalFunction;
use Delorius\Reflection\Method;

/**
 * PHP callback encapsulation.
 * @property-read bool $callable
 * @property-read string|array|\Closure $native
 * @property-read bool $static
 */
final class Callback extends Object
{
    /** @var callable */
    private $cb;

    /**
     * Factory. Workaround for missing (new Callback)->invoke() in PHP 5.3.
     * @param  mixed   class, object, callable
     * @param  string  method
     * @return Callback
     */
    public static function create($callback, $m = NULL)
    {
        return new self($callback, $m);
    }

    /**
     * @param  mixed   class, object, callable
     * @param  string  method
     * @return \Closure
     */
    public static function closure($callable, $m = NULL)
    {
        if ($m !== NULL) {
            $callable = array($callable, $m);

        } elseif (is_string($callable) && count($tmp = explode('::', $callable)) === 2) {
            $callable = $tmp;

        } elseif ($callable instanceof \Closure) {
            return $callable;

        } elseif (is_object($callable)) {
            $callable = array($callable, '__invoke');
        }

        if (PHP_VERSION_ID >= 50400) {
            if (is_string($callable) && function_exists($callable)) {
                $r = new \ReflectionFunction($callable);
                return $r->getClosure();

            } elseif (is_array($callable) && method_exists($callable[0], $callable[1])) {
                $r = new \ReflectionMethod($callable[0], $callable[1]);
                return $r->getClosure($callable[0]);
            }
        }

        self::check($callable, TRUE);
        $_callable_ = $callable;
        return function () use ($_callable_) {
            Callback::check($_callable_);
            return call_user_func_array($_callable_, func_get_args());
        };
    }

    /**
     * @return callable
     */
    public static function check($callable, $syntax = FALSE)
    {
        if (!is_callable($callable, $syntax)) {
            throw new Error($syntax
                ? 'Given value is not a callable type.'
                : "Callback '" . self::toString($callable) . "' is not callable."
            );
        }
        return $callable;
    }

    /**
     * @param  mixed   class, object, callable
     * @param  string  method
     */
    public function __construct($cb, $m = NULL)
    {
        if ($m !== NULL) {
            $cb = array($cb, $m);

        } elseif ($cb instanceof self) { // prevents wrapping itself
            $this->cb = $cb->cb;
            return;
        }

        if (!is_callable($cb, TRUE)) {
            throw new Error("Invalid callback.");
        }
        $this->cb = $cb;
    }

    /**
     * Invokes callback. Do not call directly.
     * @return mixed
     */
    public function __invoke()
    {
        if (!is_callable($this->cb)) {
            throw new Error("Callback '$this' is not callable.");
        }
        $args = func_get_args();
        return call_user_func_array($this->cb, $args);
    }

    /**
     * Invokes callback.
     * @return mixed
     */
    public static function invoke($callable)
    {
        self::check($callable);
        return call_user_func_array($callable, array_slice(func_get_args(), 1));
    }

    /**
     * Invokes callback with an array of parameters.
     * @param  array
     * @return mixed
     */
    public static function invokeArgs($callable, array $args = array())
    {
        self::check($callable);
        return call_user_func_array($callable, $args);
    }

    /**
     * Invokes internal PHP function with own error handler.
     * @param  string
     * @return mixed
     */
    public static function invokeSafe($function, array $args, $onError)
    {
        $prev = set_error_handler(function ($severity, $message, $file, $line, $context = NULL, $stack = NULL) use ($onError, & $prev, $function) {
            if ($file === '' && defined('HHVM_VERSION')) { // https://github.com/facebook/hhvm/issues/4625
                $file = $stack[1]['file'];
            }
            if ($file === __FILE__ && $onError(str_replace("$function(): ", '', $message), $severity) !== FALSE) {
                return;
            } elseif ($prev) {
                return call_user_func_array($prev, func_get_args());
            }
            return FALSE;
        });

        try {
            $res = call_user_func_array($function, $args);
            restore_error_handler();
            return $res;

        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }
    }

    /**
     * Invokes callback.
     * @return mixed
     */
    public function invoke_()
    {
        if (!is_callable($this->cb)) {
            throw new Error("Callback '$this' is not callable.");
        }
        $args = func_get_args();
        return call_user_func_array($this->cb, $args);
    }


    /**
     * Invokes callback with an array of parameters.
     * @param  array
     * @return mixed
     */
    public function invokeArgs_(array $args)
    {
        if (!is_callable($this->cb)) {
            throw new Error("Callback '$this' is not callable.");
        }
        return call_user_func_array($this->cb, $args);
    }

    /**
     * Verifies that callback can be called.
     * @return bool
     */
    public function isCallable()
    {
        return is_callable($this->cb);
    }

    /**
     * Returns PHP callback pseudotype.
     * @return string|array|\Closure
     */
    public function getNative()
    {
        return $this->cb;
    }

    /**
     * @return string
     */
    public static function toString($callable)
    {
        if ($callable instanceof \Closure) {
            if ($inner = self::unwrap($callable)) {
                return '{closure ' . self::toString($inner) . '}';
            }
            return '{closure}';
        } elseif (is_string($callable) && $callable[0] === "\0") {
            return '{lambda}';
        } else {
            is_callable($callable, TRUE, $textual);
            return $textual;
        }
    }

    /**
     * @return GlobalFunction|Method
     */
    public static function toReflection($callable)
    {
        if ($callable instanceof \Closure && $inner = self::unwrap($callable)) {
            $callable = $inner;
        } elseif ($callable instanceof Callback) {
            $callable = $callable->getNative();
        }

        $class = class_exists('Delorius\Reflection\Method') ? 'Delorius\Reflection\Method' : 'ReflectionMethod';
        if (is_string($callable) && strpos($callable, '::')) {
            return new $class($callable);
        } elseif (is_array($callable)) {
            return new $class($callable[0], $callable[1]);
        } elseif (is_object($callable) && !$callable instanceof \Closure) {
            return new $class($callable, '__invoke');
        } else {
            $class = class_exists('Delorius\Reflection\GlobalFunction') ? 'Delorius\Reflection\GlobalFunction' : 'ReflectionFunction';
            return new $class($callable);
        }
    }

    /**
     * @return bool
     */
    public function isStatic()
    {
        return is_array($this->cb) ? is_string($this->cb[0]) : is_string($this->cb);
    }

    /**
     * @return bool
     */
    public static function isStaticThis($callable)
    {
        return is_array($callable) ? is_string($callable[0]) : is_string($callable);
    }

    /**
     * Unwraps closure created by self::closure(), used i.e. by ObjectMixin in PHP < 5.4
     * @internal
     * @return callable
     */
    public static function unwrap(\Closure $closure)
    {
        $r = new \ReflectionFunction($closure);
        if (substr($r->getName(), -1) === '}') {
            $vars = $r->getStaticVariables();
            return isset($vars['_callable_']) ? $vars['_callable_'] : $closure;

        } elseif ($obj = $r->getClosureThis()) {
            return array($obj, $r->getName());

        } elseif ($class = $r->getClosureScopeClass()) {
            return array($class->getName(), $r->getName());

        } else {
            return $r->getName();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->cb instanceof \Closure) {
            return '{closure}';
        } elseif (is_string($this->cb) && $this->cb[0] === "\0") {
            return '{lambda}';
        } else {
            is_callable($this->cb, TRUE, $textual);
            return $textual;
        }
    }

}