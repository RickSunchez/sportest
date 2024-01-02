<?php
namespace CMS\Core\Component\Sitemaps;

interface IItemSitemaps {

    public function initUrls();

    public function create();

    public function getFullName();

    public function getPath($absolute = true);

} 