<?php
namespace CMS\Core\Component\Snippet\Bridges;

use Delorius\DI\CompilerExtension;


class ParserExtension extends CompilerExtension
{

    public $defaults = array(
        'tags' => array(),
    );

    public function loadConfiguration()
    {
        $configs = $this->validateConfig($this->defaults);

        $container = $this->getContainerBuilder();
        $parser = $container->addDefinition($this->prefix('parser'))
            ->setClass('\CMS\Core\Component\Snippet\Parser');

        if (count($configs['tags'])) {
            foreach ($configs['tags'] as $name => $class) {
                $parser->addSetup('addSnippet', array($name, $class));
            }
        }

        if ($this->name === 'parser') {
            $container->addAlias('parser', $this->prefix('parser'));
        }
    }

}
