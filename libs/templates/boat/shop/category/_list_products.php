<? if (count($products)): ?>
    <ul class="b-products">
        <? foreach ($products as $key => $item): ?>
            <li class="b-products__item">
                <a class="b-products__image"
                   title="<?= $this->escape($item->name) ?>"
                   href="<?= $item->link(); ?>">
                    <? if ($item->image): ?>
                        <img class="lazy" width="146" height="146"
                             data-original="/thumb/146/<?= $item->image->image_id ?>"
                             alt="<?= $this->escape($item->name); ?>"
                             src="/source/images/zero.gif">
                    <? else: ?>
                        <img src="/source/images/no.png" alt="">
                    <? endif; ?>
                </a>
                <a class="b-products__link" href="<?= $item->link(); ?>">
                    <?= $item->name; ?>
                </a>

                <div class="b-products__price">
                    <?= ($item->value > 0) ? $item->getPrice() : 'уточняйте у менеджера' ?></div>
            </li>

        <? endforeach; ?>
    </ul>

    <? if ($count): ?>
        <a class="b-products____yet-link"
           href="<?= link_to_city('shop_category_list', array('cid' => $category->pk(), 'url' => $category->url)); ?>">+
            еще <?= $count; ?> <?= \Delorius\Utils\Strings::pluralForm($count, 'товар', 'товара', 'товаров') ?> в этой
            категории</a>
    <? endif; ?>

<? endif; ?>
