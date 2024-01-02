<?php

namespace CMS\Core\Component\Header;

use CMS\Core\Component\WebLoader\Compiler;
use CMS\Core\Component\WebLoader\Filter\CssCompressFilter;
use CMS\Core\Component\WebLoader\Filter\PHPFilter;
use CMS\Core\Component\WebLoader\Filter\VariablesFilter;
use CMS\Core\Component\WebLoader\Loader\CssLoader;
use CMS\Core\Component\WebLoader\Loader\JavaScriptLoader;
use Delorius\DI\Container;
use Delorius\Exception\Error;
use Delorius\Http\Url;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;
use Delorius\View\Html;

/**
 * HeaderControl<br />
 * This renderable component is ultimate solution for valid and complete HTML headers.
 *
 * @author Ondřej Mirtes
 * @copyright (c) Ondřej Mirtes 2009, 2010
 * @license MIT
 * @package HeaderControl
 */
class HeaderControl extends RenderableContainer
{

    /**
     * doctypes
     */
    const HTML_4 = self::HTML_4_STRICT; //backwards compatibility
    const HTML_4_STRICT = 'html4_strict';
    const HTML_4_TRANSITIONAL = 'html4_transitional';
    const HTML_4_FRAMESET = 'html4_frameset';

    const HTML_5 = 'html5';

    const XHTML_1 = self::XHTML_1_STRICT; //backwards compatibility
    const XHTML_1_STRICT = 'xhtml1_strict';
    const XHTML_1_TRANSITIONAL = 'xhtml1_transitional';
    const XHTML_1_FRAMESET = 'xhtml1_frameset';

    /**
     * languages
     */
    const CZECH = 'cs';
    const SLOVAK = 'sk';
    const ENGLISH = 'en';
    const GERMAN = 'de';

    /**
     * content types
     */
    const TEXT_HTML = 'text/html';
    const APPLICATION_XHTML = 'application/xhtml+xml';

    /** @var string doctype */
    private $docType;

    /** @var bool whether doctype is XML compatible or not */
    private $xml;

    /** @var string document language */
    private $language;

    /** @var string document title */
    private $title;

    /** @var string title separator */
    private $titleSeparator;

    /** @var bool whether title should be rendered in reverse order or not */
    private $titlesReverseOrder = TRUE;

    /** @var array document hierarchical titles */
    private $titles = array();

    /** @var array site rss channels */
    private $rssChannels = array();

    /** @var array header meta tags */
    private $metaTags = array();

    /** @var array header Open Graph properties */
    private $properties = array();

    /** @var Html &lt;html&gt; tag */
    private $htmlTag;

    /** @var string document content type */
    private $contentType;

    /** @var bool whether XML content type should be forced or not */
    private $forceContentType;

    /** @var [] string path to favicon (without $basePath) */
    private $favicons = array();

    /** @var bool определяте отрисовывать шапку полность или только внутрянку */
    private $fullHeader;

    /** @var  \Delorius\Page\Pagination\PaginationBuilder */
    private $paginationBuilder;

    /** @var Container */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct();
        $this->setContentType(self::TEXT_HTML);
    }

    /**
     * @param PaginationBuilder $paginationBuilder
     * @return $this
     */
    public function setPagination(PaginationBuilder $paginationBuilder)
    {
        $this->paginationBuilder = $paginationBuilder;
        return $this;
    }

    /**
     * @return PaginationBuilder
     */
    public function getPagination()
    {
        return $this->paginationBuilder;
    }

    /**
     * @param $docType
     * @return $this
     * @throws \Delorius\Exception\Error
     */
    public function setDocType($docType)
    {
        if ($docType == self::HTML_4_STRICT || $docType == self::HTML_4_TRANSITIONAL ||
            $docType == self::HTML_4_FRAMESET || $docType == self::HTML_5 ||
            $docType == self::XHTML_1_STRICT || $docType == self::XHTML_1_TRANSITIONAL ||
            $docType == self::XHTML_1_FRAMESET
        ) {
            $this->docType = $docType;
            $this->xml = Html::$xhtml = ($docType == self::XHTML_1_STRICT ||
                $docType == self::XHTML_1_TRANSITIONAL ||
                $docType == self::XHTML_1_FRAMESET);
        } else {
            throw new Error("Doctype $docType is not supported.");
        }

        return $this; //fluent interface
    }

    /**
     * @return string
     */
    public function getDocType()
    {
        return $this->docType;
    }

    /**
     * @return bool
     */
    public function isXml()
    {
        return $this->xml;
    }

    /**
     * @param $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this; //fluent interface
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param $title
     * @param bool $clear
     * @return $this
     * @throws \Delorius\Exception\Error
     */
    public function setTitle($title, $clean = false)
    {
        if ($title != NULL && $title != '') {
            $this->title = $title;
            if ($clean) {
                $this->titles = array();
            }
        } else {
            throw new Error("Title must be non-empty string.");
        }

        return $this; //fluent interface
    }

    /**
     * @param int $index
     * @return string
     */
    public function getTitle($index = 0)
    {
        if (count($this->titles) == 0) {
            return $this->title;
        } else if (count($this->titles) - 1 - $index < 0) {
            return $this->getTitle();
        } else {
            return $this->titles[count($this->titles) - 1 - $index];
        }
    }

    /**
     * @param $title
     * @return $this
     * @throws \Delorius\Exception\Error
     */
    public function addTitle($title)
    {
        if ($this->titleSeparator) {
            $this->titles[] = $title;
        } else {
            throw new Error('Title separator is not set.');
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @param $separator
     * @return $this
     */
    public function setTitleSeparator($separator)
    {
        $this->titleSeparator = $separator;

        return $this; //fluent interface
    }

    /**
     * @return string
     */
    public function getTitleSeparator()
    {
        return $this->titleSeparator;
    }

    /**
     * @param $reverseOrder
     * @return $this
     */
    public function setTitlesReverseOrder($reverseOrder)
    {
        $this->titlesReverseOrder = (bool)$reverseOrder;

        return $this; //fluent interface
    }

    /**
     * @return bool
     */
    public function isTitlesOrderReversed()
    {
        return $this->titlesReverseOrder;
    }

    /**
     * @return string
     */
    public function getTitleString()
    {
        $tmp = $this->titles;
        if ($tmp) {
            if (!$this->titlesReverseOrder) {
                array_unshift($tmp, $this->title);
            } else {
                $tmp = array_reverse($tmp);
                ksort($tmp);
                array_push($tmp, $this->title);
            }

            $title = implode($this->titleSeparator, $tmp);

        } else {
            $title = $this->title;
        }

        if ($this->paginationBuilder) {
            if (!$this->paginationBuilder->isPageAll() && !$this->paginationBuilder->isFirst()) {
                $title .= ' ' . $this->titleSeparator . ' ' . _t('CMS:Core', 'page') . ': ' . $this->paginationBuilder->getPage();
            }
        }
        $title = Strings::firstUpper($title);
        return $title;
    }

    /**
     * @param $title
     * @param $link
     * @return $this
     */
    public function addRssChannel($title, $link)
    {
        $this->rssChannels[] = array(
            'title' => $title,
            'link' => $link,
        );

        return $this; //fluent interface
    }

    /**
     * @return array
     */
    public function getRssChannels()
    {
        return $this->rssChannels;
    }

    /**
     * @param $contentType
     * @param bool $force
     * @return $this
     * @throws \Delorius\Exception\Error
     */
    public function setContentType($contentType, $force = FALSE)
    {
        if ($contentType == self::APPLICATION_XHTML &&
            $this->docType != self::XHTML_1_STRICT && $this->docType != self::XHTML_1_TRANSITIONAL &&
            $this->docType != self::XHTML_1_FRAMESET
        ) {
            throw new Error("Cannot send $contentType type with non-XML doctype.");
        }

        if ($contentType == self::TEXT_HTML || $contentType == self::APPLICATION_XHTML) {
            $this->contentType = $contentType;
        } else {
            throw new Error("Content type $contentType is not supported.");
        }

        $this->forceContentType = (bool)$force;

        return $this; //fluent interface
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @return bool
     */
    public function isContentTypeForced()
    {
        return $this->forceContentType;
    }

    /**
     * @param $filename
     * @param bool|false $size
     * @return $this
     */
    public function setFavicons($filename, $size = false)
    {
        $this->favicons[] = array('file' => $filename, 'size' => $size);
        return $this; //fluent interface
    }

    /**
     * @return array
     */
    public function getFavicons()
    {
        return $this->favicons;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setMetaTag($name, $value)
    {
        $this->metaTags[$name] = $value;

        return $this; //fluent interface
    }

    /**
     * @param $name
     * @return string|null
     */
    public function getMetaTag($name)
    {
        return isset($this->metaTags[$name]) ? $this->metaTags[$name] : NULL;
    }

    /**
     * @return array
     */
    public function getMetaTags()
    {
        return $this->metaTags;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
        return $this; //fluent interface
    }

    /**
     * @param $name
     * @return string|null
     */
    public function getProperty($name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : NULL;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->setMetaTag('author', $author);

        return $this; //fluent interface
    }

    /**
     * @return string|null
     */
    public function getAuthor()
    {
        return $this->getMetaTag('author');
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->setMetaTag('description', $description);

        return $this; //fluent interface
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getMetaTag('description');
    }

    /**
     * @param $keywords
     * @return $this
     * @throws \Delorius\Exception\Error
     */
    public function addKeywords($keywords)
    {
        if (is_array($keywords)) {
            if ($this->keywords) {
                $this->setMetaTag('keywords', $this->getKeywords() . ', ' . implode(', ', $keywords));
            } else {
                $this->setMetaTag('keywords', implode(', ', $keywords));
            }
        } else if (is_string($keywords)) {
            if ($this->keywords) {
                $this->setMetaTag('keywords', $this->getKeywords() . ', ' . $keywords);
            } else {
                $this->setMetaTag('keywords', $keywords);
            }
        } else {
            throw new Error('Type of keywords argument is not supported.');
        }

        return $this; //fluent interface
    }

    /**
     * @return string|null
     */
    public function getKeywords()
    {
        return $this->getMetaTag('keywords');
    }

    /**
     * @param $robots
     * @return $this
     */
    public function setRobots($robots)
    {
        $this->setMetaTag('robots', $robots);

        return $this; //fluent interface
    }

    /**
     * @return string|null
     */
    public function getRobots()
    {
        return $this->getMetaTag('robots');
    }

    /**
     * @param Html $htmlTag
     * @return $this
     */
    public function setHtmlTag(\Delorius\View\Html $htmlTag)
    {
        $this->htmlTag = $htmlTag;
        return $this; // fluent interface
    }

    /**
     * @return Html
     */
    public function getHtmlTag()
    {
        if ($this->htmlTag == NULL) {
            $html = \Delorius\View\Html::el('html');

            if ($this->xml) {
                $html->attrs['xmlns'] = 'http://www.w3.org/1999/xhtml';
                $html->attrs['xml:lang'] = $this->language;
                $html->attrs['lang'] = $this->language;
            }

            if ($this->docType == self::HTML_5) {
                $html->attrs['lang'] = $this->language;
            }

            $this->htmlTag = $html;
        }

        return $this->htmlTag;
    }

    /**
     * @return string|void
     */
    public function render($source = true)
    {
        $this->renderBegin();
        if ($source)
            $this->renderSource();
        $this->renderRss();
        echo "\n";
        $this->renderPagination();
        echo "\n";
        $this->renderEnd();
        echo "\n";

    }

    public function renderSource()
    {
        $this->renderCss();
        echo "\n";
        $this->renderJs();
        echo "\n";
    }

    /**
     * @return string|void
     */
    public function renderBegin()
    {

        $response = $this->container->getService('httpResponse');
        if (
            $this->docType == self::XHTML_1_STRICT &&
            $this->contentType == self::APPLICATION_XHTML &&
            ($this->forceContentType || $this->isClientXhtmlCompatible())
        ) {
            $contentType = self::APPLICATION_XHTML;
            if (!headers_sent()) {
                $response->setHeader('Vary', 'Accept');
            }
        } else {
            $contentType = self::TEXT_HTML;
        }

        if (!headers_sent()) {
            $response->setContentType($contentType, 'utf-8');
        }

        if ($contentType == self::APPLICATION_XHTML) {
            echo "<?xml version='1.0' encoding='utf-8'?>\n";
        }

        if ($this->isFullHeader()) {
            echo $this->getDocTypeString() . "\n";
            echo $this->getHtmlTag()->startTag() . "\n";
            echo Html::el('head')->startTag() . "\n";
        }

        if ($this->docType != self::HTML_5) {
            $metaLanguage = Html::el('meta');
            $metaLanguage->attrs['http-equiv'] = 'Content-Language';
            $metaLanguage->content($this->language);
            echo $metaLanguage . "\n";
        }

        $metaContentType = Html::el('meta');
        $metaContentType->attrs['http-equiv'] = 'Content-Type';
        $metaContentType->content($contentType . '; charset=utf-8');
        echo $metaContentType . "\n";

        echo Html::el('title', array('itemprop' => 'name'))->setText($this->getTitleString()) . "\n";

        if (count($this->favicons)) {
            $host = $this->container->getService('url')->getHostUrl();
            foreach ($this->favicons as $favicon) {
                if (!$favicon['size']) {

                    echo Html::el('link')->rel('shortcut icon')
                            ->href($host . $favicon['file']) . "\n";

                } else {
                    echo Html::el('link', array('type' => 'image/x-icon', 'sizes' => $favicon['size'] . 'x' . $favicon['size']))->rel('shortcut icon')
                            ->href($host . $favicon['file']) . "\n";
                }
            }

        }

        echo Html::el('meta')->name('Generator')->content('Delorius CMF 2! - Copyright (C) 2010 All rights reserved.') . "\n";
        foreach ($this->metaTags as $name => $content) {
            if (Arrays::keysAnyoneExists('description|keywords', array($name => 0))) {
                echo Html::el('meta', array('itemprop' => $name))
                        ->name($name)
                        ->content($content) . "\n";
            } else {
                echo Html::el('meta')->name($name)->content($content) . "\n";
            }
        }
        $is_base_url = false;
        foreach ($this->properties as $name => $content) {
            if ($name == 'og:image') {
                echo Html::el('meta', array('itemprop' => 'image'))
                        ->content($content) . "\n";
            }
            if ($name == 'og:url') {
                $is_base_url = true;
                echo Html::el('base', array('itemprop' => 'url'))
                        ->href($content) . "\n";
                if (!$this->paginationBuilder || $this->paginationBuilder->isPageAll()) {
                    $url = new Url($content);
                    $url->setQueryParameter('page', null);
                    echo Html::el('link', array('rel' => 'canonical'))
                            ->href($url) . "\n";
                } else {
                    $httpRequest = $this->container->getService('httpRequest');
                    echo Html::el('link', array('rel' => 'canonical'))
                            ->href($httpRequest->getUrl()) . "\n";
                }
            }
            echo Html::el('meta')->property($name)->content($content) . "\n";
        }

        if(!$is_base_url){
            $httpRequest = $this->container->getService('httpRequest');
            echo Html::el('link', array('rel' => 'canonical'))
                    ->href($httpRequest->getUrl()) . "\n";
            echo Html::el('base', array('itemprop' => 'url'))
                    ->href($httpRequest->getUrl()->getAbsoluteUrlNoQuery()) . "\n";
        }
    }


    /**
     * Pagination seo
     * @return string|void
     */
    public function renderPagination()
    {
        if ($this->paginationBuilder) {
            if ($this->paginationBuilder->getPageCount() > 0) {

                if (!$this->paginationBuilder->isFirst()) {
                    echo Html::el('link')->rel('prev')
                            ->href($this->paginationBuilder->getUrlPage($this->paginationBuilder->getPage() - 1)) . "\n";
                }

                if (!$this->paginationBuilder->isLast()) {
                    echo Html::el('link')->rel('next')
                            ->href($this->paginationBuilder->getUrlPage($this->paginationBuilder->getPage() + 1)) . "\n";
                    echo Html::el('link')->rel('last')
                            ->href($this->paginationBuilder->getUrlPage($this->paginationBuilder->getLastPage())) . "\n";
                }

            }
        }
    }

    /**
     * @return string|void
     */
    public function renderEnd()
    {
        if ($this->isFullHeader()) {
            echo Html::el('head')->endTag();
        }
    }

    /**
     * @return string|void
     */
    public function renderRss($channels = NULL)
    {
        if ($channels !== NULL) {
            $this->rssChannels = array();

            foreach ($channels as $title => $link) {
                $this->addRssChannel($title, $link);
            }
        }

        foreach ($this->rssChannels as $channel) {
            echo Html::el('link')->rel('alternate')->type('application/rss+xml')
                    ->title($channel['title'])
                    ->href('#') . "\n";
        }
    }

    protected function createComponentCss()
    {
        $site = $this->container->getService('site');
        $browser = $this->container->getService('browser');
        $filesCss = $this->container->getService('cssFiles');
        $parameters = $this->container->getParameters();

        // save temp
        $tempFiles = (array)$filesCss->getFiles();
        $tempRemoteFiles = (array)$filesCss->getRemoteFiles();
        $filesCss->clear();

        if (
            $site->mobile &&
            $browser->isMobile() &&
            !$browser->isFullVersion()
        ) {
            $template = $site->mobile;
        } else {
            $template = $site->template;
        }


        if (count($parameters['webloader']['css'][$template]['files'])) {
            $filesCss->addFiles($parameters['webloader']['css'][$template]['files']);
        }
        if (count($parameters['webloader']['css'][$template]['remoteFiles'])) {
            $filesCss->addRemoteFiles($parameters['webloader']['css'][$template]['remoteFiles']);
        }

        //load temp
        $filesCss->addFiles($tempFiles);
        $filesCss->addRemoteFiles($tempRemoteFiles);

        $compiler = Compiler::createCssCompiler($filesCss, $parameters['webloader']['temp'], $template);

        //filters
        if (count($parameters['webloader']['css'][$template]['variables']))
            $compiler->addFilter(new VariablesFilter((array)$parameters['webloader']['css'][$template]['variables']));

        $compiler->addFilter(new CssCompressFilter());

        $css = new CssLoader($compiler, $parameters['webloader']['path']);
        $css->setMedia('screen');
        return $css;
    }

    protected function createComponentJs()
    {
        $site = $this->container->getService('site');
        $browser = $this->container->getService('browser');
        $filesJs = $this->container->getService('jsFiles');
        $parameters = $this->container->getParameters();

        if (
            $site->mobile &&
            $browser->isMobile() &&
            !$browser->isFullVersion()
        ) {
            $template = $site->mobile;
        } else {
            $template = $site->template;
        }

        // save temp
        $tempFiles = (array)$filesJs->getFiles();
        $tempRemoteFiles = (array)$filesJs->getRemoteFiles();
        $filesJs->clear();

        if (count($parameters['webloader']['js'][$template]['files'])) {
            $filesJs->addFiles($parameters['webloader']['js'][$template]['files']);
        }
        if (count($parameters['webloader']['js'][$template]['remoteFiles'])) {
            $filesJs->addRemoteFiles($parameters['webloader']['js'][$template]['remoteFiles']);
        }

        //load temp
        $filesJs->addFiles($tempFiles);
        $filesJs->addRemoteFiles($tempRemoteFiles);

        $compiler = Compiler::createJsCompiler($filesJs, $parameters['webloader']['temp'], $template);

        //filters
        if (count($parameters['webloader']['js'][$template]['variables']))
            $compiler->addFilter(new VariablesFilter($parameters['webloader']['js'][$template]['variables']));

        $compiler->addFilter(new PHPFilter());

        $js = new JavaScriptLoader($compiler, $parameters['webloader']['path']);
        return $js;
    }


    /**
     * Определяет всю шапку строить если true
     * и только внутрености если fakse
     * @return bool
     */
    private function isFullHeader()
    {
        return $this->fullHeader;
    }


    private function getDocTypeString($docType = NULL)
    {
        if ($docType == NULL) {
            $docType = $this->docType;
        }

        switch ($docType) {
            case self::HTML_4_STRICT:
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
                break;
            case self::HTML_4_TRANSITIONAL:
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
                break;
            case self::HTML_4_FRAMESET:
                return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
                break;
            case self::HTML_5:
                return '<!DOCTYPE html>';
                break;
            case self::XHTML_1_STRICT:
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
                break;
            case self::XHTML_1_TRANSITIONAL:
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
                break;
            case self::XHTML_1_FRAMESET:
                return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
                break;
            default:
                throw new Error("Doctype $docType is not supported.");
        }
    }

    private function isClientXhtmlCompatible()
    {
        $req = $this->container->getService('httpRequest');
        return stristr($req->getHeader('Accept'), 'application/xhtml+xml') ||
            $req->getHeader('Accept') == '*/*';
    }

}
