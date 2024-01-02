<?php
namespace CMS\Core\Component\Header\Bridges;

use Delorius\DI\CompilerExtension;


class HeaderExtension extends CompilerExtension
{

    protected $favicons = array();

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $header = $container->addDefinition($this->prefix('header'))
            ->setClass('\CMS\Core\Component\Header\HeaderControl')
            ->addSetup('setDocType', array(\CMS\Core\Component\Header\HeaderControl::HTML_5))
            ->addSetup('setLanguage', array('en'));

        if (file_exists(DIR_INDEX . '/favicon.ico')) {
            $this->favicons[] = array('file' => '/favicon.ico', 'size' => false);
        }

        if (file_exists(DIR_INDEX . '/favicon-16x16.png')) {
            $this->favicons[] = array('file' => '/favicon-16x16.png', 'size' => 16);
        }

        if (file_exists(DIR_INDEX . '/favicon-32x32.png')) {
            $this->favicons[] = array('file' => '/favicon-32x32.png', 'size' => 32);
        }

        if ($this->name === 'header') {
            $container->addAlias('header', $this->prefix('header'));
        }

    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->getConfig();

        $name = $this->prefix('header');
        $initialize->addBody('$header = $this->getService(?);', array($name));
        $initialize->addBody('$domain = getHostParameter("_route");');
        $initialize->addBody('$parameters = ?;', array($config));

        if(count($this->favicons)){
            foreach($this->favicons as $favicon){
                $initialize->addBody('$header->setFavicons(?,?);',array($favicon['file'],$favicon['size']));
            }
        }

        $initialize->addBody('
        if (!isset($parameters[$domain])) {
            $arr = $parameters["default"];
        } else {
            $arr = $parameters[$domain];
        }

        #title
        $header->setTitle($arr["title"]);
        if (!empty($arr["separator"]))
            $header->setTitleSeparator($arr["separator"]);
        $header->setTitlesReverseOrder($arr["reverse_order"]);

        #header->seo
        if (!empty($arr["keys"]))
            $header->addKeywords($arr["keys"]);
        if (!empty($arr["description"]))
            $header->setDescription($arr["description"]);
        if (!empty($arr["robots"]))
            $header->setRobots($arr["robots"]);

        #meta
        if (sizeof($arr["meta"])) {
            foreach ($arr["meta"] as $name => $content) {
                $header->setMetaTag($name, $content);
            }
        }
        #og
        if (sizeof($arr["open.graf"])) {
            foreach ($arr["open.graf"] as $name => $content) {
                if ($name == "image" && $content) {
                    $content = \CMS\Core\Helper\Helpers::canonicalUrl($content);
                }
                $header->setProperty("og:".$name, $content);
            }
        }
        #head->attr
        if (sizeof($arr["head"]["attr"])) {
            foreach ($arr["head"]["attr"] as $attr => $value) {
                $header->getHtmlTag()->attrs[$attr] = $value;
            }
        }
        ');




    }


}
