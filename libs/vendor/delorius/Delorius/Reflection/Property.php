<?php
namespace Delorius\Reflection;

use Delorius\Core\ObjectMixin;



/**
 * Reports information about a classes variable.
 *
 *
 * @property-read ClassType $declaringClass
 * @property-read IAnnotation[][] $annotations
 * @property-read string $description
 * @property-read string $name
 * @property  mixed $value
 * @property-read bool $public
 * @property-read bool $private
 * @property-read bool $protected
 * @property-read bool $static
 * @property-read bool $default
 * @property-read int $modifiers
 * @property-read string $docComment
 * @property-write bool $accessible
 */
class Property extends \ReflectionProperty
{

	public function __toString()
	{
		return 'Property ' . parent::getDeclaringClass()->getName() . '::$' . $this->getName();
	}



	/********************* Reflection layer ****************d*g**/



	/**
	 * @return ClassType
	 */
	public function getDeclaringClass()
	{
		return new ClassType(parent::getDeclaringClass()->getName());
	}



	/********************* Annotations support ****************d*g**/



	/**
	 * Has property specified annotation?
	 * @param  string
	 * @return bool
	 */
	public function hasAnnotation($name)
	{
		$res = AnnotationsParser::getAll($this);
		return !empty($res[$name]);
	}



	/**
	 * Returns an annotation value.
	 * @param  string
	 * @return IAnnotation
	 */
	public function getAnnotation($name)
	{
		$res = AnnotationsParser::getAll($this);
		return isset($res[$name]) ? end($res[$name]) : NULL;
	}



	/**
	 * Returns all annotations.
	 * @return IAnnotation[][]
	 */
	public function getAnnotations()
	{
		return AnnotationsParser::getAll($this);
	}



	/**
	 * Returns value of annotation 'description'.
	 * @return string
	 */
	public function getDescription()
	{
		return $this->getAnnotation('description');
	}



	/********************* Object behaviour ****************d*g**/



	/**
	 * @return ClassType
	 */
	public static function getReflection()
	{
		return new ClassType(get_called_class());
	}



	public function __call($name, $args)
	{
		return ObjectMixin::call($this, $name, $args);
	}



	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}



	public function __set($name, $value)
	{
		ObjectMixin::set($this, $name, $value);
	}



	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}



	public function __unset($name)
	{
		ObjectMixin::remove($this, $name);
	}

}
