<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Catalog\Entity\Category;

class CategorySitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'categories';
    protected $router;
    protected $typeId;
    protected $typeName;

    public function initUrls()
    {
        $categories = Category::model()
            ->type($this->typeId)
            ->select('cid', 'url', 'pid', 'date_cr', 'date_edit')
            ->active()
            ->order_by('date_edit','desc')
            ->order_by('date_cr')
            ->sort();

        if (count($this->options['no_cid'])) {
            $categories->where('cid', 'not in', $this->options['no_cid']);
        }
        $result = $categories->find_all();
        foreach ($result as $item) {
            $this->addUrl(
                link_to($this->router, array(
                        'cid' => $item['cid'],
                        'url' => $item['url'])
                ),
                $item['date_edit'] ? $item['date_edit'] : $item['date_cr'],
                self::CHANGE_MONTHLY,
                $item['pid'] == 0 ? 1 : 0.7
            );
        }
    }


    public function getFullName()
    {
        return $this->site . '_' . $this->name . '_' . $this->typeName;
    }
}