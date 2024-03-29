<?php
namespace Delorius\PhpGenerator;

use Delorius\Core\Object;

/**
 * Method parameter description.
 */
class Parameter extends Object
{
	/** @var string */
	private $name = '';

	/** @var bool */
	private $reference = FALSE;

	/** @var string|NULL */
	private $typeHint;

	/** @var bool */
	private $optional = FALSE;

	/** @var mixed */
	public $defaultValue;


	/**
	 * @return self
	 */
	public static function from(\ReflectionParameter $from)
	{
		$param = new static($from->getName());
		$param->reference = $from->isPassedByReference();
		if (PHP_VERSION_ID >= 70000) {
			$param->typeHint = $from->hasType() ? (string) $from->getType() : NULL;
		} elseif ($from->isArray()) {
			$param->typeHint = 'array';
		} elseif (PHP_VERSION_ID >= 50400 && $from->isCallable()) {
			$param->typeHint = 'callable';
		} else {
			try {
				$param->typeHint = $from->getClass() ? $from->getClass()->getName() : NULL;
			} catch (\ReflectionException $e) {
				if (preg_match('#Class (.+) does not exist#', $e->getMessage(), $m)) {
					$param->typeHint = $m[1];
				} else {
					throw $e;
				}
			}
		}
		$param->optional = PHP_VERSION_ID < 50407 ? $from->isOptional() || ($param->typeHint && $from->allowsNull()) : $from->isDefaultValueAvailable();
		$param->defaultValue = (PHP_VERSION_ID === 50316 ? $from->isOptional() : $from->isDefaultValueAvailable()) ? $from->getDefaultValue() : NULL;
		return $param;
	}


	/**
	 * @param  string  without $
	 */
	public function __construct($name = '')
	{
		$this->setName($name);
	}


	/**
	 * @param  string  without $
	 * @return self
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @param  bool
	 * @return self
	 */
	public function setReference($state = TRUE)
	{
		$this->reference = (bool) $state;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isReference()
	{
		return $this->reference;
	}


	/**
	 * @param  string|NULL
	 * @return self
	 */
	public function setTypeHint($hint)
	{
		$this->typeHint = $hint ? (string) $hint : NULL;
		return $this;
	}


	/**
	 * @return string|NULL
	 */
	public function getTypeHint()
	{
		return $this->typeHint;
	}


	/**
	 * @param  bool
	 * @return self
	 */
	public function setOptional($state = TRUE)
	{
		$this->optional = (bool) $state;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isOptional()
	{
		return $this->optional;
	}


	/**
	 * @return self
	 */
	public function setDefaultValue($val)
	{
		$this->defaultValue = $val;
		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

}
