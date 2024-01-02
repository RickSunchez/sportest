<?php
namespace Delorius\DI;

/**
 * NulledExtension
 */
class NulledExtension extends CompilerExtension
{

    /** @var bool */
    private $debugMode;

    public function __construct($debugMode = FALSE)
    {
        $this->debugMode = $debugMode;
    }

    public function loadConfiguration()
    {
        if($this->debugMode){
            df_print_r($this->name);
            df_print_r($this->getConfig());
        }
    }

}
