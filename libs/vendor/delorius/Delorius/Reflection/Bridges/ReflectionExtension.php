<?php
namespace Delorius\Reflection\Bridges;

use Delorius\DI\CompilerExtension;

/**
 * Extension for Nette DI.
 */
class ReflectionExtension extends CompilerExtension
{
	/** @var bool */
	private $debugMode;


	public function __construct($debugMode = FALSE)
	{
		$this->debugMode = $debugMode;
	}


	public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
	{
		$class->getMethod('initialize')
			->addBody('Delorius\Reflection\AnnotationsParser::setCacheStorage($this->getByType("Delorius\Caching\IStorage"));')
			->addBody('Delorius\Reflection\AnnotationsParser::$autoRefresh = ?;', array($this->debugMode));
	}

}
