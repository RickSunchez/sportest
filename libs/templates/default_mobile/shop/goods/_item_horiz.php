<div class="b-goods-item__layout" data-href="<?= $goods->link() ?>">

    <div class="l-goods-item__image">

        <? if ($goods->image): ?>
            <img data-href="<?= $goods->link() ?>" class=" b-goods-item__image
                 photo" src="<?= $goods->image->preview ?>"/>
        <? else: ?>
            <img data-href="<?= $goods->link() ?>" class=" b-goods-item__image"
                 src="/source/images/no.png" alt=""/>
        <? endif; ?>

    </div>

    <div class="l-goods-item__info">
        <div class="b-goods-item__cat"><?= product_cat($goods->cid) ?></div>
        <a class="b-goods-item__link / fn /" href="<?= $goods->link() ?>"><?= $goods->name ?></a>

        <div class="b-goods-item__price">
            <? if ($goods->value > 0): ?>
                <?= $goods->getPrice() ?>
            <? else: ?>
                Дог.
            <? endif; ?>
        </div>


    </div>


</div>