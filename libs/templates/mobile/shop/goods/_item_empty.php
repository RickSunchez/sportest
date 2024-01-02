<section class="b-like-product__item">
    <div class="b-like-product__link-image" data-href="<?= $goods->link(); ?>">
        <? if ($goods->image): ?>
            <img class="b-like-product__image / photo" alt="<?= $goods->name ?>" src="/thumb/180/<?= $goods->image->image_id ?>">
        <? endif; ?>
    </div>
    <div class="b-like-product__info">
        <h4 class="b-like-product__name">
            <a class="b-like-product__link" title="<?= $this->escape($goods->name); ?>"
               href="<?= $goods->link(); ?>"><?= $goods->name; ?></a>
        </h4>

        <div class="b-like-product__price">
            <?= $goods->getPrice(); ?>
        </div>
    </div>
</section>