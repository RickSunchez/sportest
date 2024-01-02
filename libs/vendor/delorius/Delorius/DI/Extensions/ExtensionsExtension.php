<?php
namespace Delorius\DI\Extensions;

use Delorius\DI\CompilerExtension;

/**
 * Enables registration of other extensions in $config file
 */
class ExtensionsExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		foreach ($this->getConfig() as $name => $class) {
			if ($class instanceof \Delorius\DI\Statement) {
				$rc = new \ReflectionClass($class->getEntity());
				$this->compiler->addExtension($name, $rc->newInstanceArgs($class->arguments));
			} else {
				$this->compiler->addExtension($name, new $class);
			}
		}
	}

}
