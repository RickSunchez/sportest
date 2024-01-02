<?php
namespace Delorius\DI\Extensions;

use Delorius\DI\CompilerExtension;

/**
 * DI extension.
 */
class DIExtension extends CompilerExtension
{
	public $defaults = array(
		'debugger' => FALSE,
		'accessors' => FALSE,
	);

	/** @var bool */
	private $debugMode;

	/** @var int */
	private $time;


	public function __construct($debugMode = FALSE)
	{
		$this->debugMode = $debugMode;
		$this->time = microtime(TRUE);
	}


	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults);
		if ($config['accessors']) {
			$this->getContainerBuilder()->parameters['container']['accessors'] = TRUE;
		}
	}


	public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
	{
		$initialize = $class->getMethod('initialize');
		$initialize->addBodyClass($this);
		$container = $this->getContainerBuilder();

		if ($this->debugMode && $this->config['debugger']) {
			//debugger
		}

		foreach (array_filter($container->findByTag('run')) as $name => $on) {
			$initialize->addBody('$this->getService(?);', array($name));
		}

		if (!empty($this->config['accessors'])) {
			$definitions = $container->getDefinitions();
			ksort($definitions);
			foreach ($definitions as $name => $def) {
				if (\Delorius\PhpGenerator\Helpers::isIdentifier($name)) {
					$type = $def->getImplement() ?: $def->getClass();
					$class->addDocument("@property $type \$$name");
				}
			}
		}
	}

}
