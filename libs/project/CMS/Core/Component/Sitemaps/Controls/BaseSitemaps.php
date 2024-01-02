<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Core\Component\Sitemaps\IItemSitemaps;
use Delorius\ComponentModel\Component;
use Delorius\Core\Environment;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Path;
use Delorius\Utils\Strings;

abstract class BaseSitemaps extends Component implements IItemSitemaps
{
    const CHANGE_ALWAYS = 'always';
    const CHANGE_HOURLY = 'hourly';
    const CHANGE_DAILY = 'daily';
    const CHANGE_WEEKLY = 'weekly';
    const CHANGE_MONTHLY = 'monthly';
    const CHANGE_YEARLY = 'yearly';
    const CHANGE_NEVER = 'never';

    /** @var string */
    protected $name = 'base';
    /** @var string */
    protected $site = 'www';

    /** @var array */
    protected $options;

    private $_array = array();

    public function __construct($options = array())
    {
        $this->monitor('CMS\Core\Component\Sitemaps\Collection');
        parent::__construct();
        $this->setOptions($options);
    }

    public function getFullName()
    {
        return $this->site . '_' . $this->name;
    }

    public function getPath($absolute = true)
    {
        $dir = Environment::getContext()->getParameters('path.sitemaps');
        $path_xml = _sf($dir . '/{0}.xml', $this->getFullName());
        if (!$absolute) {
            $path_xml = Path::localPath(DIR_INDEX, $path_xml);
        }
        return $path_xml;
    }

    public function setOptions($options)
    {
        if (isset($options['site'])) {
            $this->site = $options['site'];
            unset($options['site']);
        }

        if (isset($options['name'])) {
            $this->name = $options['name'];
            unset($options['name']);
        }

        $this->options = $options;
        return $this;
    }


    public function create()
    {
        $this->initUrls();
        $bits = $this->createXML();
        return $bits;
    }

    protected function addUrl($loc, $lastmod = null, $changefreq = null, $priority = 0.5)
    {
        $arr = array();
        $arr['loc'] = Strings::escape($loc);
        if (!$lastmod) {
            $lastmod = time();
        }
        $arr['lastmod'] = date('Y-m-d', $lastmod);
        if ($changefreq) {
            $arr['changefreq'] = $changefreq;
        }
        if ($priority) {
            $arr['priority'] = $priority;
        }
        $this->_array[] = $arr;
    }

    protected function createXML()
    {
        if (count($this->_array)) {
            $dir = Environment::getContext()->getParameters('path.sitemaps');
            FileSystem::createDir($dir);

            $objDom = new \DOMDocument('1.0');
            $objDom->encoding = 'UTF-8';
            $objDom->formatOutput = true;
            $objDom->preserveWhiteSpace = false;

            $root = $objDom->createElement("urlset");
            $objDom->appendChild($root);

            $root_attr = $objDom->createAttribute("xmlns");
            $root->appendChild($root_attr);

            $root_attr_text = $objDom->createTextNode("http://www.sitemaps.org/schemas/sitemap/0.9");
            $root_attr->appendChild($root_attr_text);

            foreach ($this->_array as $row) {
                $url = $objDom->createElement("url");
                $root->appendChild($url);

                $loc = $objDom->createElement("loc");
                $lastmod = $objDom->createElement("lastmod");
                $changefreq = $objDom->createElement("changefreq");
                $priority = $objDom->createElement("priority");


                if ($row["loc"]) {
                    $url->appendChild($loc);
                    $url_text = $objDom->createTextNode($row["loc"]);
                    $loc->appendChild($url_text);
                }

                if ($row["lastmod"]) {
                    $url->appendChild($lastmod);
                    $lastmod_text = $objDom->createTextNode($row["lastmod"]);
                    $lastmod->appendChild($lastmod_text);
                }

                if ($row["changefreq"]) {
                    $url->appendChild($changefreq);
                    $changefreq_text = $objDom->createTextNode($row["changefreq"]);
                    $changefreq->appendChild($changefreq_text);
                }

                $url->appendChild($priority);
                $priority_text = $objDom->createTextNode($row["priority"]);
                $priority->appendChild($priority_text);

            }
            $bits = $objDom->save($dir . '/' . $this->getFullName() . '.xml');
            $objDom = null;
            return $bits;
        }

        return 0;
    }

}