<div class="b-goods-item__layout" data-href="<?= $goods->link() ?>">

    <div class="l-goods-item__image" data-href="<?= $goods->link() ?>">
        <? if ($goods->image): ?>
            <img class="b-goods-item__image / photo"
                 src="/thumb/180/<?= $goods->image->image_id ?>"
                 alt="<?= $this->escape($goods->name) ?>"/>
        <? else: ?>
            <img class="b-goods-item__image"
                 src="/source/images/no.png" alt=""/>
        <? endif; ?>

        <? if ($per = $goods->getPerDiscount()): ?>
            <div class="b-goods__disc" title="Скидка <?= $per ?>%">
                - <?= $per; ?> %
            </div>
        <? endif; ?>

        <? if ($goods->is_amount == 0): ?>
            <div class="b-goods__none-text">
                Отсуствует на складе
            </div>
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