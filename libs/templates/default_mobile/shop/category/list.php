<article class="b-page__content">

    <h1 class="b-page__title"><?= $category->getHeaderTitle(); ?></h1>

    <? if ($category && $category->text_top): ?>
        <section class="b-category__text b-category__text_top b-text">
            <?= $category->text_top; ?>
        </section>
    <? endif; ?>


    <? if (count($categories)): ?>
        <div class="b-category__layout b-category__layout_white hListing">
            <? foreach ($categories as $cat): ?>

                <section class="b-subcategories__layout">

                    <h2 class="b-subcategories__title">
                        <a href="<?= link_to_city('shop_category_list', array('cid' => $cat->pk(), 'url' => $cat->url)) ?>">
                            <?= $cat->name ?>
                        </a>
                    </h2>

                    <? if ($cat->children): ?>
                        <?= $this->action('Shop:Catalog:Shop:sub', array('categoryId' => $cat->pk(), 'theme' => 'image', 'image' => true)); ?>
                    <? else: ?>
                        //product
                    <? endif; ?>


                </section>
            <? endforeach; ?>
        </div>
    <? endif; ?>


    <? if ($category && $category->text_below): ?>
        <section class="b-category__text b-category__text_sub b-category__text_below b-text">
            <?= $category->text_below; ?>
        </section>
    <? endif; ?>


</article>