<article class="b-category b-category_full" itemscope itemtype="http://schema.org/ItemList">

    <header class="b-category__header">
        <h1 itemprop="name" class="b-category__title"><?= $category->getHeaderTitle() ?></h1>
    </header>

    <?= $this->action('Boat:Store:Html:collectionProduct', array('categoryId' => $category->pk())); ?>

    <div class="b-table">
        <div class="b-table-cell l-category__content">

            <? if ($category && $category->text_top): ?>
                <section class="b-category__text b-category__text_top b-text">
                    <?= $category->text_top; ?>
                </section>
            <? endif; ?>


            <? if (count($categories)): ?>
                <div class="b-category__layout hListing">
                    <? foreach ($categories as $cat): ?>

                        <section class="b-subcategories__layout" id="cat_<?= $cat->pk() ?>">
                            <h2 class="b-subcategories__title">
                                <a href="<?= link_to_city('shop_category_list', array('cid' => $cat->pk(), 'url' => $cat->url)) ?>">
                                    <?= $cat->name ?>
                                </a>
                            </h2>

                            <? if ($cat->children): ?>
                                <?= $this->action('Shop:Catalog:Shop:sub', array('categoryId' => $cat->pk(), 'theme' => 'sub', 'image' => true)); ?>
                            <? elseif ($cat->goods): ?>
                                <?= $this->action('Boat:Store:Html:products', array(
                                    'limit' => 5,
                                    'category' => $cat
                                )) ?>
                            <? endif; ?>

                        </section>
                    <? endforeach; ?>
                </div>
            <? endif; ?>

        </div>

        <div class="b-table-cell l-category__menu">
            <div class="b-category__menu" role="navigation">
                <div class="b-filters__title">Категории</div>
                <? foreach ($categories as $cat): ?>
                    <a class="b-category__item-link" href="#cat_<?= $cat->pk() ?>">
                        <?= $cat->name; ?> (<?=$cat->goods?>)
                    </a>
                <? endforeach; ?>
            </div>
        </div>

    </div>

    <? if ($category && $category->text_below): ?>
        <section class="b-category__text b-category__text_below b-text">
            <?= $category->text_below; ?>
        </section>
    <? endif; ?>


</article>