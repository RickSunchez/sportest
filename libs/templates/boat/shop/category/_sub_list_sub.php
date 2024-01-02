<? if (count($categories)): ?>
    <ul class="b-subcategories-list">
        <? foreach ($categories as $key => $category): ?>
            <? if ($category['goods'] != 0): ?>
                <? $i = $key + 1; ?>

                <? if ($i <= 5): ?>
                    <li class="b-subcategory__item">
                        <a class="b-subcategory__image"
                           href="<?= link_to_city('shop_category_list', array('cid' => $category['id'], 'url' => $category['url'])); ?>">
                            <? if (isset($images[$category['id']])): ?>
                                <img class="lazy" width="146" height="146"
                                     data-original="/thumb/146/<?= $images[$category['id']]['image_id'] ?>"
                                     src="/source/images/zero.gif"
                                     alt="<?= $this->escape($category['name']); ?>">
                            <? else: ?>
                                <img src="/source/images/no.png" alt="">
                            <? endif; ?>
                        </a>
                        <a class="b-subcategory__link"
                           href="<?= link_to_city('shop_category_list', array('cid' => $category['id'], 'url' => $category['url'])); ?>">
                            <?= $category['name'] ?>
                        </a>

                        <div class="b-subcategory__product">
                            <?= $category['goods'] ?> <?= \Delorius\Utils\Strings::pluralForm($count, 'товар', 'товара', 'товаров') ?>
                        </div>
                    </li>

                    <? unset($categories[$key]) ?>
                <? else: ?>
                    <? break; ?>
                <? endif; ?>

            <? endif ?>
        <? endforeach; ?>
    </ul>
    <? $count = count($categories) ?>
    <? if ($count): ?>

        <div class="b-subcategories__yet">

            <a class="js-yet b-subcategories__yet-link" href="javascript:;">+
                еще <?= $count; ?> <?= \Delorius\Utils\Strings::pluralForm($count, 'категория', 'категории', 'категорий') ?></a>

            <ul class="b-subcategories-list">
                <? foreach ($categories as $category): ?>
                    <? if ($category['goods'] != 0): ?>
                        <li class="b-subcategory__item">
                            <a class="b-subcategory__image"
                               href="<?= link_to_city('shop_category_list', array('cid' => $category['id'], 'url' => $category['url'])); ?>">
                                <? if (isset($images[$category['id']])): ?>
                                    <img width="196" height="196"
                                         src="/thumb/196/<?= $images[$category['id']]['image_id'] ?>"
                                         alt="<?= $this->escape($category['name']); ?>">
                                <? else: ?>
                                    <img src="/source/images/no.png" alt="">
                                <? endif; ?>
                            </a>
                            <a class="b-subcategory__link"
                               href="<?= link_to_city('shop_category_list', array('cid' => $category['id'], 'url' => $category['url'])); ?>">
                                <?= $category['name'] ?>
                            </a>

                            <div class="b-subcategory__product">
                                <?= $category['goods'] ?> <?= \Delorius\Utils\Strings::pluralForm($count, 'товар', 'товара', 'товаров') ?>
                            </div>
                        </li>
                    <? endif; ?>
                <? endforeach; ?>
            </ul>

        </div>

    <? endif; ?>
<? endif; ?>
