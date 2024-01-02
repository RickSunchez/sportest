<div class="b-catalog b-catalog_goods b-page-show">
    <h1 class="b-page-show__title"><?= $category->name ?></h1>

    <? if ($category && $category->text_top): ?>
    <div class="b-catalog__top b-page-show__text">
        <?= $category->text_top; ?>
    </div>
    <?endif;?>


    <div class="b-goods-list">
        <ul class="b-goods-list__layout hListing">

            <? foreach ($goods as $item): ?>
                <li class="b-goods-item b-goods-item_<?= $item->pk() ?>" id="goods_<?= $item->pk() ?>  item hproduct"  >
                    <div class="b-goods-item__layout">


                        <a title="<?= $this->escape($item->name); ?>"
                           class="b-link b-goods-item__link b-goods-item__link_image url"
                           href="<?=$item->link()?>">

                            <? if ($item->image): ?>
                                <img class="b-goods-item__image photo" src="<?= $item->image->preview ?>"/>
                            <? else: ?>
                                <div class="b-no-photo b-goods-item__no-foto"></div>
                            <? endif; ?>

                        </a>

                        <div class="b-goods-item__info">

                            <h2 class="b-goods-item__name">
                                <a title="<?= $this->escape($item->name); ?>"
                                   class="b-link b-goods-item__link b-goods-item__link_name fn url"
                                   href="<?=$item->link()?>">
                                        <?= $item->name ?>
                                </a>
                            </h2>

                            <div class="b-goods-item__article">
                                Арт.: <?= $item->article; ?>
                            </div>

                            <div class="b-goods-item__price-box">
                                <span class="price"><?= $item->getPrice()?></span>
                            </div>

                        </div><!-- .b-goods-item__info -->


                        <div class="b-goods-item__cart">
                            <div class="b-goods-item__quantity">

                                <a href="javascript:;" class="b-goods-item__minus" onclick="MinusQuantity('<?= $item->pk(); ?>','<?=$item->getMinimum()?>')">-</a>

                                <input type="text"
                                       value="<?= $basket->getQuantity($item->pk()) ? $basket->getQuantity($item->pk()) : $item->getMinimum() ?>"
                                       class="js-cart__quantity"
                                       onblur="ChangeQuantity('<?= $item->pk(); ?>','<?=$item->getMinimum()?>')"/>

                                <a href="javascript:;" class="b-goods-item__plus" onclick="PlusQuantity('<?= $item->pk(); ?>','<?=$item->getMinimum()?>')">+</a>

                            </div>

                            <div class="b-goods-item__cart-btn">


                                <a class="js-cart__btn <?= $basket->getQuantity($item->pk()) ? 'js-cart__btn_in' : '' ?> <?= $item->amount > 0 ? '' : 'js-cart__btn_none' ?>"
                                   title="<?= $basket->getQuantity($item->pk()) ? 'Товар в корзине' : '' ?> <?= $item->amount > 0 ? '' : 'Товара нет на складе' ?> "
                                   href="javascript:;" onclick="AddBasketGoods('<?= $item->pk(); ?>','<?=$item->getMinimum()?>');">
                                    <?if($item->amount==0):?>
                                        Товара нет на складе
                                    <?elseif($basket->getQuantity($item->pk())):?>
                                        Товар в корзине
                                    <?else:?>
                                        Добавить
                                    <?endif;?>
                                </a>


                            </div>

                        </div><!-- .b-goods-item__cart -->



                    </div>
                </li>
            <?endforeach?>

        </ul>
    </div>


    <?= $pagination->render(); ?>


    <? if ($category && $category->text_below): ?>
        <div class="b-catalog__below b-page-show__text">
            <?=$category->text_below;?>
        </div>
    <?endif;?>



</div>