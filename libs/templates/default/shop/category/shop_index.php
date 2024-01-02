<? if (count($categories[0])): ?>
    <div class="b-categories-index hListing" itemscope itemtype="http://schema.org/ItemList">

        <? foreach ($categories[0] as $patent): ?>
            <article class="b-categories-index__parent item" itemprop="itemListElement">
                <h2 class="b-parent__title">
                    <a href="<?= link_to('shop_category_list', array('cid' => $patent['cid'], 'url' => $patent['url'])); ?>"><?= $patent['name'] ?></a>
                </h2>             

                <? if (count($categories[$patent['cid']])): ?>
                    <ul class="b-category-index__layout">

                        <? foreach ($categories[$patent['cid']] as $key => $category): ?>
                            <? $i = $key + 1; ?>

                            <? if ($i <= 3): ?>
                                <li class="b-category-index__item">
                                    <div class="b-category-index__link-image" title="<?= $category['name'] ?>"
                                         data-href="<?= link_to('shop_category_list', array('cid' => $category['cid'], 'url' => $category['url'])); ?>">
                                        <? if (isset($images[$category['cid']])): ?>
                                            <img class="lazy b-category-index__image"
                                                 data-original="<?= $images[$category['cid']]['preview'] ?>"
                                                 alt="<?= $category['name'] ?>"
                                                 src="/source/images/no.png">
                                        <? else: ?>
                                            <img class="b-category-index__image" src="/source/images/no.png" alt="">
                                        <? endif; ?>
                                    </div>
                                    <a class="b-category-index__link"
                                       href="<?= link_to('shop_category_list', array('cid' => $category['cid'], 'url' => $category['url'])); ?>"
                                        ><?= $category['name'] ?></a>

                                    <p class="b-category-index__count"><?= $category['goods']; ?> <?= \Delorius\Utils\Strings::pluralForm($category['goods'], 'товар', 'товара', 'товаров') ?></p>
                                </li>
                                <? unset($categories[$patent['cid']][$key]) ?>
                            <? else: ?>
                                <? break; ?>
                            <? endif; ?>

                        <? endforeach; ?>
                    </ul>
                <? endif; ?>


                <? if (count($categories[$patent['cid']])): ?>
                    <div class="b-category-index__layout-order">
                        <? foreach ($categories[$patent['cid']] as $key => $category): ?>
                            <? $i = $key + 1; ?>

                            <? if ($i <= 9): ?>
                                <span class="b-category-index__item-order">
                                    <a class="b-category-index__link-order"
                                       href="<?= link_to('shop_category_list', array('cid' => $category['cid'], 'url' => $category['url'])); ?>"
                                        ><?= $category['name'] ?></a>
                                <span>(<?= $category['goods']; ?>)</span></span>

                                <? unset($categories[$patent['cid']][$key]) ?>
                            <? else: ?>
                                <? break; ?>
                            <? endif; ?>

                        <? endforeach; ?>
                    </div>
                <? endif; ?>

                <? if (count($categories[$patent['cid']])): ?>
                    <div class="b-category-index__layout-yet">
                        + <a href="<?= link_to('shop_category_list', array('cid' => $patent['cid'], 'url' => $patent['url'])); ?>">
                            еще <?= count($categories[$patent['cid']]) ?> <?= \Delorius\Utils\Strings::pluralForm(count($categories[$patent['cid']]), 'категория', 'категории', 'категорий') ?>
                        </a>
                    </div>
                <? endif; ?>


            </article>
        <? endforeach; ?>

    </div>
<? endif; ?>

<?= $this->action('Shop:Commodity:Goods:listType', array('typeId' => 5,'limit'=>6)); ?>
<?= $this->action('Shop:Commodity:Goods:listType', array('typeId' => 6,'limit'=>6)); ?>
