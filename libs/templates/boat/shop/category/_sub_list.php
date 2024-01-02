<? if (count($categories)): ?>
    <ul class="b-cats-sub-inline">
        <? foreach ($categories as $category): ?>
            <li class="b-cats-sub-inline__item">
                <a class="b-cats-sub-inline__link"
                   href="<?= link_to_city('shop_category_list', array('cid' => $category['id'], 'url' => $category['url'])); ?>">
                    <?= $category['name'] ?> <span>(<?= $category['goods'] ?>)</span>
                </a>
            </li>
        <? endforeach; ?>
    </ul>
<? endif; ?>
