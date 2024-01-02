<?php
namespace Delorius\Core\Bridges;

use Delorius\DI\CompilerExtension;
use Delorius\Tools\Debug\Toolbar;


class ORMExtension extends CompilerExtension
{
    /**
     * @var bool
     */
    protected $debugMode;

    public function __construct($debugMode = FALSE)
    {
        $this->debugMode = $debugMode;
    }

    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->getConfig();

        #behaviors
        if (count($config)) {

            foreach ($config as $orm => $cnf) {

                if (count($cnf['behaviors'])) {
                    $initialize->addBody($orm . '::update_behaviors(?);', array($cnf['behaviors']));
                }

                if (count($cnf['table_columns'])) {
                    $initialize->addBody($orm . '::update_table_columns(?);', array($cnf['table_columns']));
                }

            }


        }


    }


}
