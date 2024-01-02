<article class="b-page__content">
    <h1 class="b-page__title"><?= $category->name ?></h1>

    <? if (count($goods)): ?>

        <aside class="l-nav-sort">
            <div class="b-table">
                <div class="b-table-cell l-nav-sort__btn">
                    <div data-open="sort" class="b-sort__btn"></div>
                </div>
                <div class="b-table-cell l-nav-sort__categories">
                    <div data-open="categories" class="b-category-model__btn">
                        Категории ↓
                    </div>
                </div>
            </div>
        </aside>
        <?= $this->action('Shop:Catalog:Shop:sub', array('categoryId' => $category->pk(), 'theme' => 'model','image'=>true)); ?>
        <?= $this->action('Shop:Commodity:Goods:filterSort') ?>

        <ul class="b-goods-list__layout hListing">

            <? foreach ($goods as $item): ?>
                <li class="b-goods-item b-goods-item_<?= $item->pk() ?>"
                    id="goods_<?= $item->pk() ?> item hproduct">

                    <?= $this->partial('shop/goods/_item_horiz', array(
                        'goods' => $item,
                        'basket' => $basket
                    )) ?>

                </li>
            <? endforeach ?>

        </ul>

        <?= $pagination->render(); ?>

    <? else: ?>
        <?= $this->action('Shop:Catalog:Shop:sub', array('categoryId' => $category->pk())); ?>
    <? endif; ?>

    <? if ($category && $category->text_below): ?>
        <section class="b-category__text b-category__text_below b-text">
            <?= $category->text_below; ?>
        </section>
    <? endif; ?>


</article>