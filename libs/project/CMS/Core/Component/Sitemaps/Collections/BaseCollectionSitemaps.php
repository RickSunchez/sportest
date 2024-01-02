<?php

namespace CMS\Core\Component\Sitemaps\Collections;

use CMS\Core\Component\Sitemaps\Controls\BaseSitemaps;
use Delorius\Core\Environment;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Strings;


abstract class BaseCollectionSitemaps extends BaseSitemaps
{

    private $_sitemaps = array();


    /**
     * @param CommonSitemaps $sitemaps
     */
    protected function addSitemaps(CommonSitemaps $sitemaps)
    {
        $arr = array();
        $arr['loc'] = Strings::escape($sitemaps->location());
        $arr['lastmod'] = date('Y-m-d');
        $this->_sitemaps[] = $arr;
    }

    protected function createXML()
    {
        if (count($this->_sitemaps)) {
            $dir = Environment::getContext()->getParameters('path.sitemaps');
            FileSystem::createDir($dir);

            $objDom = new \DOMDocument('1.0');
            $objDom->encoding = 'UTF-8';
            $objDom->formatOutput = true;
            $objDom->preserveWhiteSpace = false;

            $root = $objDom->createElement("sitemapindex");
            $objDom->appendChild($root);

            $root_attr = $objDom->createAttribute("xmlns");
            $root->appendChild($root_attr);

            $root_attr_text = $objDom->createTextNode("http://www.sitemaps.org/schemas/sitemap/0.9");
            $root_attr->appendChild($root_attr_text);

            foreach ($this->_sitemaps as $row) {
                $url = $objDom->createElement("sitemap");
                $root->appendChild($url);

                $loc = $objDom->createElement("loc");
                $lastmod = $objDom->createElement("lastmod");


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

            }
            $bits = $objDom->save($dir . '/' . $this->getFullName() . '.xml');
            $objDom = null;
            return $bits;
        }

        return 0;
    }

}