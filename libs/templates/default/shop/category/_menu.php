<div class="b-menu-left">
    <div class="title">КАТАЛОГ ТОВАРОВ</div>
    <ul class="menu_block">
        <? foreach ($categories as $cat): ?>
            <li>
                <a href="<?= link_to('category_list', array('url' => $cat->url, 'cid' => $cat->pk())) ?>"><?= $cat->name ?></a>
                <? if ( ($cat->pk() == $parentId) && $childCategories): ?>
                    <ul>
                        <? foreach ($childCategories as $sunMenu): ?>
                            <li>
                                <a href="<?= link_to('shop_category_list', array('url' => $sunMenu->url, 'cid' => $sunMenu->pk())) ?>">
                                    <?= $sunMenu->name;?>
                                </a>
                            </li>
                        <? endforeach; ?>
                    </ul>
                <? endif; ?>
            </li>
        <? endforeach; ?>
    </ul>
</div>