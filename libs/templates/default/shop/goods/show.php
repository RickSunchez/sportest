<article class="b-goods / item hproduct / " role="article" data-id="<?= $goods->pk()?>">
    <header class="b-goods__header">
        <h1 class="b-goods__title fn"><?= $goods->name ?></h1>
        <? if ($goods->article): ?>
            <div class="b-goods__article">Артикул: <?= $goods->article ?></div>
        <? endif; ?>
    </header>

    <section class="b-goods__body">

        <div class="b-table l-goods__top">
            <div class="b-table-cell l-goods__carousel">

                <? if (count($images)): ?>
                    <? foreach ($images as $image): ?>
                        <div class="b-table b-goods__image">
                            <div class="b-table-cell">
                                <img class="photo" data-lazy="<?= $image->normal ?>" alt="<?= $image->name ?>">
                            </div>
                        </div>

                    <? endforeach ?>
                <? endif; ?>


            </div>
            <div class="b-table-cell l-goods__basket">

                <div class="b-goods__basket-col">

                    <? if ($goods->value_old > 0): ?>
                        <div class="b-goods__price-old"><?= $goods->getPriceOld(); ?></div>
                    <? endif; ?>
                    <div class="b-goods__price"><?= $goods->getPrice(); ?></div>

                    <div class="b-goods__counter">
                        <a class="minus" href="javascript:;">-</a>
                        <input class="quantity" type="text" value="1">
                        <a class="minus" href="javascript:;">+</a>
                    </div>

                    <a class="b-btn b-btn_goods" href="javascript:;">В корзину</a>

                    <a onclick="one_click();" class="b-goods__one-click" href="javascript:;">Купить в 1 клик</a>

                    <ul class="b-goods__conditions">
                        <li>Доставка осуществляется в период с 10:00 до
                            21:00. Расчетное значение даты и времени
                            возможной доставки в выбранном городе
                        </li>
                        <li>Самовывоз до 18:00. Мы соберем, отложим
                            ваш товар и приготовим его к указанному
                            времени
                        </li>
                        <li>
                            Способы оплаты Вы можете оплатить Ваш
                            заказ наличным или безналичным расчетом
                        </li>
                    </ul>

                </div>
            </div>
        </div>

        <? if (count($images) > 1): ?>
            <div class="b-goods__carousel-nav">

                <? foreach ($images as $image): ?>
                    <div class="item">
                        <img src="<?= $image->preview ?>" alt="<?= $image->name ?>">
                    </div>
                <? endforeach; ?>

            </div>
        <? endif; ?>


        <section id="sections" class="b-sections">
            <header class="b-sections__nav">
                <? foreach ($sections as $key => $section): ?>

                    <? //section?>
                    <? if (($tab == '' && $key == 0) || ($tab == $section->url)): ?>
                        <h1><?= $section->name; ?></h1>
                    <? else: ?>
                        <a href="<?= link_to('shop_goods_tab', array('url' => $goods->url, 'id' => $goods->pk(), 'tab' => $key != 0 ? $section->url : '')) ?>#sections"
                            ><span><?= $section->name; ?></span></a>
                    <? endif; ?>

                    <? //characteristics?>
                    <? if ($key == 0): ?>
                        <? if (sizeof($characteristics)): ?>
                            <? if ($tab == 'characteristics'): ?>
                                <h1>Характеристики</h1>
                            <? else: ?>
                                <a href="<?= link_to('shop_goods_tab', array('id' => $goods->pk(), 'url' => $goods->url, 'tab' => 'characteristics')) ?>#sections"
                                    ><span>Характеристики</span></a>
                            <? endif; ?>
                        <? endif ?>
                    <? endif; ?>

                <? endforeach ?>

                <? //reviews?>
                <? if ($tab == 'reviews'): ?>
                    <h1>Отзывы (<?= $goods->votes ?>)</h1>
                <? else: ?>
                    <a href="<?= link_to('shop_goods_tab', array('id' => $goods->pk(), 'url' => $goods->url, 'tab' => 'reviews')) ?>"
                        ><span>Отзывы (<?= $goods->votes ?>)</span></a>
                <? endif; ?>

            </header>
            <section class="b-sections__text b-text description">

                <? foreach ($sections as $key => $section): ?>
                    <? if (($tab == '' && $key == 0) || ($tab == $section->url)): ?>
                        <? ($key == 0) or DI()->getService('header')->AddTitle($section->name); ?>
                        <?= $section->text ?>
                    <? endif ?>
                <? endforeach; ?>


                <? if ($tab == 'characteristics'): ?>
                    <? DI()->getService('header')->AddTitle('Характеристики') ?>

                    <? foreach ($characteristics as $chara): ?>
                        <? if (count($chara['values'])): ?>
                            <? if (count($chara['values'])): ?>
                                <h2><?= $chara['group']['name'] ?></h2>
                                <div class="b-characteristics identifier">
                                    <? foreach ($chara['values'] as $key => $item): ?>
                                        <div class="b-characteristics__item">
                                            <span class="b-characteristics__name"><span
                                                    class="type"><?= $item['chara']['name'] ?></span></span>
                                            <span class="b-characteristics__value value"
                                                ><?= $item['value']['name'] ?> <?= $item['value']['unit'] ?></span>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            <? endif; ?>
                        <? else: ?>
                            <? if (count($chara)): ?>
                                <div class="b-characteristics identifier">
                                    <? foreach ($chara as $key => $item): ?>
                                        <div class="b-characteristics__item">
                                            <span class="b-characteristics__name"><span
                                                    class="type"><?= $item['chara']['name'] ?></span></span>
                                            <span class="b-characteristics__value value"
                                                ><?= $item['value']['name'] ?> <?= $item['value']['unit'] ?></span>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            <? endif; ?>
                        <? endif; ?>
                    <? endforeach; ?>

                <? endif ?>
                <? if ($tab == 'reviews'): ?>
                    <? DI()->getService('header')->AddTitle('Отзывы') ?>
                    <?= $this->action('Shop:Commodity:Review:index', array('goods' => $goods)); ?>
                <? endif ?>


            </section>
        </section>

    </section>

    <footer class="b-goods__footer">

        <?= $this->action('Shop:Commodity:Goods:accompanies', array(
            'goods_id' => $goods->pk(),
            'type_id' => \Shop\Commodity\Entity\Accompany::TYPE_OTHER
        )); ?>


        <?= $this->action('Shop:Commodity:Goods:youWatched'); ?>

    </footer>
</article>
