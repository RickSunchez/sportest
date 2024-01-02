<?php
namespace Delorius\DI\Extensions;

use Delorius\DI\CompilerExtension;
use Delorius\Exception\InvalidState;
use Delorius\Exception\NotSupported;

/**
 * PHP directives definition.
 */
class PhpExtension extends CompilerExtension
{

    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {

        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $initialize->addBody('\Delorius\Core\Environment::setContext($this);');
        $initialize->addBody('        
            if (function_exists("iconv") && version_compare(PHP_VERSION, "5.6", "<")){
                iconv_set_encoding("internal_encoding", "UTF-8");
                iconv_set_encoding("input_encoding", "UTF-8");
                iconv_set_encoding("output_encoding", "UTF-8");
		    }elseif (version_compare(PHP_VERSION, "5.6", ">=")){
                ini_set("default_charset", "UTF-8");
		    }    
        ');
        $initialize->addBody('extension_loaded("mbstring") && mb_internal_encoding("UTF-8");');
        foreach ($this->getConfig() as $name => $value) {
            if ($value === NULL) {
                continue;

            } elseif (!is_scalar($value)) {
                throw new InvalidState("Configuration value for directive '$name' is not scalar.");

            } elseif ($name === 'include_path') {
                $initialize->addBody('set_include_path(?);', array(str_replace(';', PATH_SEPARATOR, $value)));

            } elseif ($name === 'ignore_user_abort') {
                $initialize->addBody('ignore_user_abort(?);', array($value));

            } elseif ($name === 'max_execution_time') {
                $initialize->addBody('set_time_limit(?);', array($value));

            } elseif ($name === 'error.reporting') {
                $initialize->addBody('Error_Reporting(?);', array($value));
            } elseif ($name === 'date.timezone') {
                $initialize->addBody('date_default_timezone_set(?);', array($value));

            } elseif (function_exists('ini_set')) {
                $initialize->addBody('ini_set(?, ?);', array($name, $value));

            } elseif (ini_get($name) != $value) { // intentionally ==
                throw new NotSupported('Required function ini_set() is disabled.');
            }
        }
    }

}
