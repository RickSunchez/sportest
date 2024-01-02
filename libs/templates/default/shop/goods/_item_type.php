<section class="b-type__item">
    <div class="b-type__link_image" data-href="<?= $goods->link(); ?>">
        <? if ($goods->image): ?>
            <img class="photo lazy" alt=""
                 data-original="<?= $goods->image->preview ?>"
                 src="/source/images/no.png">
        <? else: ?>
            <img src="/source/images/no.png" alt="">
        <? endif; ?>
    </div>
    <h3>
        <a class="b-type__link" title="<?= $this->escape($goods->name); ?>"
           href="<?= $goods->link(); ?>"><?= $goods->name; ?></a>
    </h3>

    <div class="b-type__price">
        <?= $goods->getPrice(); ?>
    </div>
</section>