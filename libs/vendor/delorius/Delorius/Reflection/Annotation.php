<?php
namespace Delorius\Reflection;

use Delorius\Core\Object;

/**
 * Basic annotation implementation.
 */
class Annotation extends Object implements IAnnotation
{

	public function __construct(array $values)
	{
		foreach ($values as $k => $v) {
			$this->$k = $v;
		}
	}



	/**
	 * Returns default annotation.
	 * @return string
	 */
	public function __toString()
	{
		return $this->value;
	}

}
