<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Page;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\Environment;

class HelpPageBehavior extends ORMBehavior
{

    private static $_pages = array();

    private static $_host = null;


    /**
     * @return string
     */
    public function link()
    {
        if (!$this->getOwner()->loaded())
            return 'javascript:;';

        return $this->linkById(
            $this->getOwner()->pk(),
            $this->getOwner()->site
        );
    }

    /**
     * @param $pageId
     * @param null $site
     * @return string
     * @throws \Delorius\Exception\Error
     */
    public function linkById($pageId, $site = null)
    {
        if ($site == null) {
            $current = Page::model()
                ->select('site')
                ->where('id', '=', (int)$pageId)
                ->find();
            $site = $current['site'];
        }

        $pages = $this->getPages($site);
        if (!isset($pages[$pageId])) {
            return 'javascript:;';
        }

        $link = '';
        if ($pages[$pageId]['main']) {
            return '/';
        } elseif ($pages[$pageId]['redirect']) {
            return $pages[$pageId]['redirect'];
        }

        $host = $this->getHost($site);
        if (0 == $pages[$pageId]['pid'])
            return $host . '/' . $pages[$pageId]['url'] . '.html';

        while (true) {
            if (0 == (int)$pages[$pageId]['pid']) {
                $link = '/' . $pages[$pageId]['url'] . $link;
                return $host . $link . '.html';
            } else {
                $link = '/' . $pages[$pageId]['url'] . $link;
                $pageId = $pages[$pageId]['pid'];
            }
        }
        return $host . $link;
    }

    /**
     * @param $pageId
     * @return string
     */
    public function titleById($pageId, $short = true)
    {
        $pages = $this->getPages();
        if (!isset($pages[$pageId])) {
            return '';
        }

        return $short ? $pages[$pageId]['short_title'] : $pages[$pageId]['title'];
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->getOwner()->children ? true : false;
    }

    /**
     * @return array
     */
    public function getPages($site = 'www')
    {
        if (!sizeof(self::$_pages[$site])) {
            $list = Page::model()
                ->select('main', 'children', 'short_title', 'title', 'id', 'pid', 'redirect', 'url')
                ->site($site)
                ->cached()
                ->find_all();
            foreach ($list as $page) {
                self::$_pages[$site][$page['id']] = $page;
            }
        }
        return self::$_pages[$site];
    }

    /**
     * @return mixed|null|string
     * @throws \Delorius\Exception\Error
     */
    protected function getHost($site = 'www')
    {
        if (self::$_host[$site] == null) {
            /** @var \Delorius\Routing\DomainRouter $domainRouter */
            $domainRouter = Environment::getContext()->getService('domainRouter');
            self::$_host[$site] = $domainRouter->generate($site);
        }
        return self::$_host[$site];
    }

} 