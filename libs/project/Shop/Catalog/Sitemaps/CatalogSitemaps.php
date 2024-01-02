<?php
namespace Shop\Catalog\Sitemaps;

use CMS\Core\Component\Sitemaps\Controls\BaseSitemaps;
use Shop\Catalog\Entity\Category;

class CatalogSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'category';

    public function initUrls()
    {
        $this->name .= '_' . $this->options['type_id'];
        $categories = Category::model()
            ->select('cid', 'url', 'pid', 'date_cr', 'date_edit')
            ->type($this->options['type_id'])
            ->active()
            ->order_by('date_edit','desc')
            ->order_by('date_cr')
            ->find_all();
        foreach ($categories as $item) {
            $this->addUrl(
                link_to($this->options['router'], array('url' => $item['url'], 'cid' => $item['cid'])),
                $item['date_edit'] ? $item['date_edit'] : $item['date_cr'],
                self::CHANGE_MONTHLY,
                $item['pid'] == 0 ? 1 : 0.7
            );
        }
    }
}