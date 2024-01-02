<?php

namespace CMS\Admin\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Caching\Cache;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Finder;

/**
 * @Template(name=admin)
 * @Admin
 */
class HomeController extends Controller
{

    /** @SetTitle Административная панель */
    public function indexAction()
    {
        $this->response($this->view->load('cms/home/index'));
    }


    /**
     * @Post
     */
    public function cleanCacheAction()
    {
        @set_time_limit(0);
        @ignore_user_abort(1);
        $this->container->getService('cache')->clean(array(Cache::ALL => true));
        $dir = $this->container->getParameters('path.temp') . '/cache';
        FileSystem::delete($dir);
        FileSystem::createDir($dir);
        FileSystem::write($dir . '/.gitkeep', '');
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function cleanCacheThumbAction()
    {
        @set_time_limit(0);
        @ignore_user_abort(1);
        $path = $this->container->getParameters('thumb.path');
        FileSystem::delete($path);
        FileSystem::createDir($path);
        $this->response(array('ok'));
    }


    /**
     * @Post
     */
    public function cleanCacheThemeAction()
    {
        @set_time_limit(0);
        @ignore_user_abort(1);

        $path = $this->container->getParameters('webloader.temp');
        $files = array();
        foreach (Finder::findFiles('*.js')->from($path) as $file) {
            $files[] = $file->getPathname();
        }
        foreach (Finder::findFiles('*.css')->in($path) as $file) {
            $files[] = $file->getPathname();
        }
        foreach (Finder::findFiles('*.gz')->in($path) as $file) {
            $files[] = $file->getPathname();
        }

        foreach ($files as $link) {
            @unlink($link);
        }

        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function createSitemapsAction()
    {
        @set_time_limit(0);
        @ignore_user_abort(1);
        $this->container->getService('sitemaps')->create();
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function clearSitemapsAction()
    {
        @set_time_limit(0);
        @ignore_user_abort(1);
        $dir = $this->container->getParameters('path.sitemaps');
        FileSystem::delete($dir);
        FileSystem::createDir($dir);
        $this->response(array('ok'));
    }

}