<?php
namespace CMS\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use CMS\Core\Entity\Image;
use CMS\Core\Entity\Page;
use Delorius\Caching\Cache;
use Delorius\Core\Environment;
use Delorius\Utils\Arrays;
use Delorius\View\Html;
use Delorius\View\View;

class PageTag extends AParserRenderer
{
    /** @var  \Delorius\Caching\Cache */
    protected $cache;

    public function before()
    {
        $this->cache = Environment::getContext()->getService('cache')->derive('page_tag');
    }


    public function render()
    {
        if (array_key_exists('child', $this->query)) {
            return $this->childRender($this->path, $this->query['child']);
        }

        if (array_key_exists('text', $this->query)) {
            $page = Page::model($this->path);
            if ($page->loaded()) {
                return Environment::getContext()->getCloneService('parser')->html($page->text);
            } else {
                return '';
            }
        }

        $links = $this->cache->load('links');
        if (!isset($links[$this->path])) {
            $link = Page::model()->linkById($this->path);
            $links[$this->path] = $link;
            $dp[Cache::TAGS][] = Page::model()->table_name();
            $this->cache->save('links', $links, $dp);
        } else {
            $link = $links[$this->path];
        }
        return $link;
    }

    public function childRender($pageId, $theme = null)
    {
        $theme = $theme ? '_' . $theme : '';

        $parentId = 0;
        if ($pageId != 0) {
            $parent = new Page($pageId);
            if (!$parent->loaded() || !$parent->hasChildren()) {
                return '';
            }
            $parentId = $parent->pk();
            $var['parent'] = $parent;
        }

        $pages = Page::model()->where('pid', '=', $parentId)->active()->cached()->sort()->find_all();
        if (!count($pages)) {
            return '';
        }
        $var['pages'] = $ids = array();
        foreach ($pages as $page) {
            $ids[] = $page->pk();
            $var['pages'][] = $page;
        }

        if (sizeof($ids)) {
            $images = Image::model()->whereByTargetType(Page::model())->cached()->whereByTargetId($ids)->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $view = new View();
        return $view->load('cms/page/_child' . $theme, $var);
    }

}