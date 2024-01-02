<?php
namespace Shop\Catalog\Sitemaps;

use CMS\Core\Component\Sitemaps\Controls\BaseSitemaps;
use Shop\Catalog\Entity\Collection;

class CollectionCatalogSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'collection_category';

    public function initUrls()
    {
        $this->name .= '_' . $this->options['type_id'];
        $collections = Collection::model()
            ->select('id', 'url', 'date_cr', 'date_edit')
            ->where('type_id','=',$this->options['type_id'])
            ->active()
            ->order_by('date_edit','desc')
            ->order_by('date_cr')
            ->find_all();
        foreach ($collections as $item) {
            $this->addUrl(
                link_to($this->options['router'], array('url' => $item['url'], 'id' => $item['id'])),
                $item['date_edit'] ? $item['date_edit'] : $item['date_cr'],
                self::CHANGE_MONTHLY,
                $item['pid'] == 0 ? 1 : 0.7
            );
        }
    }
}