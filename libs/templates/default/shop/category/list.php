<div class="b-page-show b-page-show_catalog">
    <? if ($category): ?>
        <h1 class="b-page-show__title" ><?= $category->name ?></h1>
    <? else: ?>
        <h1  class="b-page-show__title" >Каталог</h1>
    <? endif ?>
    <? if ($category && $category->text_top): ?>
    <div class="b-catalog__top b-page-show__text">
        <?=$category->text_top;?>
    </div>
    <?endif;?>
    <? if (count($categories)): ?>
        <ul class="b-catalog__layout">

            <? foreach ($categories as $cat): ?>
                <li class="b-catalog-item">
                    <a class="b-link b-catalog-item__link"
                       href="<?= link_to('category_list', array('cid' => $cat->pk(), 'url' => $cat->url)) ?>"
                        title="<?= $this->escape($cat->name);?>">
                        <? if ($images[$cat->pk()]): ?>
                            <img class="b-catalog-item__image" src="<?= $images[$cat->pk()]->preview ?>"/>
                        <? else: ?>
                            <div class="b-no-photo b-catalog-item__no-foto"></div>
                        <? endif; ?>
                        <h2 class="b-catalog-item__name"><?= $cat->name ?></h2>
                    </a>
                </li>
            <? endforeach ?>

        </ul>
    <? endif; ?>

    <? if ($category && $category->text_below): ?>
        <div class="b-catalog__below b-page-show__text">
            <?=$category->text_below;?>
        </div>
    <?endif;?>

</div>
<!-- .b-catalog -->


