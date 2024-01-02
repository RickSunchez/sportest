<section class="b-popup b-popup_option">
    <h1 class="b-popup__title"><?= $option->name ?>: <?= $variant->name; ?></h1>

    <div class="b-popup__layout">


        <div class="b-table">
            <? if ($image->loaded()): ?>
                <div class="b-table-cell b-variant__image">
                    <img src="<?= $image->preview ?>" alt="<?= $image->name ?>"/>
                </div>
            <? endif; ?>
            <div class="b-table-cell b-variant__desc">

                <?= $variant->text ?>

            </div>
        </div>

    </div>
</section>