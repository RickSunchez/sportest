<article class="b-page__content">

    <h1 class="b-page__title"><?= $category->getHeaderTitle(); ?> в <?= snippet('city', 'name', array('v' => 2)) ?></h1>

    <? if ($category && $category->text_top): ?>
        <section class="b-category__text b-category__text_top b-text">
            <?= $category->text_top; ?>
        </section>
    <? endif; ?>


    <? if (count($categories)): ?>
        <ul class="b-category__layout hListing">
            <? foreach ($categories as $cat): ?>

                <li class="b-category__item b-table item">
                    <a href="<?= link_to('shop_category_list', array('cid' => $cat->pk(), 'url' => $cat->url)) ?>"
                       class="b-category__link b-table-cell url">
                        <span class="name"><?= $cat->name ?></span> <span class="count">(<?= $cat->goods ?>)</span>
                    </a>
                </li>

            <? endforeach; ?>
        </ul>
    <? endif; ?>

    <?= $this->action('Shop:Catalog:Shop:popular', array('category' => $category)) ?>


    <? if ($category && $category->text_below): ?>
        <section class="b-category__text b-category__text_sub b-category__text_below b-text">
            <?= $category->text_below; ?>
        </section>
    <? endif; ?>


</article>