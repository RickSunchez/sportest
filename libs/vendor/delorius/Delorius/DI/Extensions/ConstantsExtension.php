<?php
namespace Delorius\DI\Extensions;

use Delorius\DI\CompilerExtension;

/**
 * Constant definitions.
 */
class ConstantsExtension extends CompilerExtension
{

	public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
	{
		foreach ($this->getConfig() as $name => $value) {
			$class->getMethod('initialize')->addBody('define(?, ?);', array($name, $value));
		}
	}

}
