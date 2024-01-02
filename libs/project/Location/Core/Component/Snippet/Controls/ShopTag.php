<?php

namespace Location\Core\Component\Snippet\Controls;

use Shop\Catalog\Entity\Category;

class ShopTag extends \Shop\Core\Component\Snippet\Controls\ShopTag
{


    protected function getLinkCategory(Category $category)
    {
        $config = $this->getConfigShop($category->type_id);
        return link_to_city($config['router'], array('cid' => $category->pk(), 'url' => $category->url));
    }

}