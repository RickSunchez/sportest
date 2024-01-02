<div class="b-page-show b-page-show_catalog">
    <h1 class="b-page-show__title"><?= $category->name?></h1>
    <?if($category->text_below):?>
        <div class="b-page-show__text">
            <?= $category->text_below?>
        </div>
    <?endif;?>
</div>