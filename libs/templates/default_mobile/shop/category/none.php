<article class="b-page__content">

    <h1 class="b-page__title"><?= $category->getHeaderTitle() ?></h1>

    <?= $this->action('Shop:Catalog:Shop:sub', array('cid' => $category->pk())); ?>

    <? if ($category && $category->text_top): ?>
        <article class="b-category__text-top b-page-show__text b-text">
            <?= $category->text_top; ?>
        </article>
    <? endif; ?>


    <? if ($category && $category->text_below): ?>
        <article class="b-page-show__text b-category__text b-text">
            <?= $category->text_below; ?>
        </article>
    <? endif; ?>
   

</article>