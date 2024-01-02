<ul class="b-subcategories-list ">
    <? foreach ($categories as $category): ?>
        <li class="b-subcategory__item">
            <a class="b-subcategory__image"
               href="<?= link_to_city('shop_category_list', array('cid' => $category['id'], 'url' => $category['url'])); ?>">
                <? if (isset($images[$category['id']])): ?>
                    <img src="/thumb/180/<?= $images[$category['id']]['image_id'] ?>"
                         alt="<?= $this->escape($category['name']); ?>">
                <? else: ?>
                    <img src="/source/images/no.png" alt="">
                <? endif; ?>
            </a>
            <a class="b-subcategory__link"
               href="<?= link_to_city('shop_category_list', array('cid' => $category['id'], 'url' => $category['url'])); ?>">
                <?= $category['name'] ?>
            </a>
            <div class="b-subcategory__product"> <?= $category['goods'] ?> <?= \Delorius\Utils\Strings::pluralForm($count, 'товар', 'товара', 'товаров') ?></div>
        </li>
    <? endforeach; ?>
</ul>