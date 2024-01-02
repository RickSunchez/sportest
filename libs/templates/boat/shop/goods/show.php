<? $attrs = $goods->getAttributes(); ?>
<? $count_attrs = count($attrs) ?>
<? $chars = $goods->getGroupCharacteristics() ?>
<? $count_chars = count($chars) ?>
<? $vendor = $goods->getVendor() ?>
<article class="b-product / item hproduct /" role="article" id="product_<?= $goods->pk() ?>"
         itemscope itemtype="http://schema.org/Product">

    <meta itemprop="productID" content="<?= $goods->pk(); ?>">
    <span class="category meta" itemprop="category"><?= product_cat($goods->cid) ?></span>


    <header class="b-product__header">
        <h1 class="b-product__title  / fn / " itemprop="name"><?= $goods->name; ?></h1>
    </header>

    <div class="b-product__layout">
        <div class="b-product__left">

            <div class="b-product__info">
                <div class="b-product__images">

                    <? if ($per = $goods->getPerDiscount()): ?>
                        <div class="b-product__disc" title="Скидка <?= $per ?>%">
                            - <?= $per; ?> %
                        </div>
                    <? endif; ?>

                    <div class="b-product__carousel js-gallery">
                        <? if (count($images)): ?>
                            <? foreach ($images as $image): ?>
                                <div class="b-table l-product__image">
                                    <div class="b-table-cell">
                                        <a  href="<?= $image->normal ?>"><img
                                                    src="<?= $image->normal ?>" alt="<?= $image->name ?>"></a>
                                    </div>
                                </div>
                            <? endforeach ?>
                        <? else: ?>
                            <img alt="" src="/source/images/no.png">
                        <? endif; ?>
                    </div>

                    <? if (count($images) > 1): ?>
                        <div class="b-product__carousel-nav">

                            <? foreach ($images as $image): ?>
                                <div class="item">
                                    <img class="phone" src="<?= $image->preview ?>" alt="<?= $image->name ?>">
                                </div>
                            <? endforeach; ?>

                        </div>
                    <? endif; ?>


                </div>
                <div class="b-product__misc">

                    <ul class="b-product__attrs">

                        <?php
                            // @note категории запчастей
                            $categoryPartsIds = [86, 87, 88, 89, 90, 91, 129, 137, 139, 153];
                            if (in_array($goods->cid, $categoryPartsIds) && $goods->t_article):
                        ?>
                            <li class="b-product__attr / identifier /">
                                <span class="type">Артикул: </span>
                                <span class="name"><?= $goods->t_article ?></span>
                            </li>

                        <? elseif ($goods->article): ?>
                            <li class="b-product__attr / identifier /">
                                <span class="type">Артикул: </span>
                                <span class="name"><?= $goods->article ?></span>
                            </li>
                        <? endif; ?>

                        <?php
                            // @note категории запчастей
                            if (in_array($goods->cid, $categoryPartsIds) && $goods->a_articles):
                                // @note дополнительные артикулы
                                if ($goods->a_articles):
                                    $articles = json_decode($goods->a_articles);
                                    if (is_object($articles)):
                                        $articles = (array)$articles;
                                        foreach ($articles as $article):
                            ?>
                                
                                            <li class="b-product__attr / identifier /">
                                                <span class="type"><?= $article->name ?>:</span>
                                                <span class="name"><?= $article->value ?></span>
                                            </li>
                                
                            <?php
                                        endforeach;
                                    endif;
                                endif;
                            ?>
                        <? endif; ?>
                        
                        <li class="b-product__attr / identifier /">
                            <span class="type">В наличии: </span>
                            <span class="name"><?= ($goods->is_amount) ? 'есть' : 'предзаказ' ?></span>
                        </li>
                        <li class="b-product__attr / identifier /"
                            itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                            <span itemprop="price" class=" meta price"><?= $goods->getPrice(false, false) ?></span>
                            <span itemprop="priceCurrency" class=" meta currency"><?= $goods->code ?></span>

                            <span class="type">Цена: </span>
                            <span
                                    class="name"><?= ($goods->value > 0) ? _sf('<span class="b-product__price js-price">{0}</span>', $goods->getPrice()) : 'уточняйте у менеджера' ?></span>
                            <? if ($goods->getPerDiscount()): ?>
                                <span class="discount"><?= $goods->getPriceOld(); ?></span>
                                <span class="discount-per">-<?= $goods->getPerDiscount(); ?>%</span>
                            <? endif; ?>
                        </li>
                        <?= $this->action('Boat:Store:Schema:li', array('pid' => $goods->pk())) ?>
                    </ul>

                    <?= $this->action('Shop:Commodity:Goods:collectionProduct'); ?>

                    <? if ($goods->is_amount): ?>
                        <a class="b-btn-product-add <?= $basket->getQuantity($goods->combination_hash) != 0 ? 'b-btn-product-add--inner' : '' ?>"
                           onclick="add_cart(<?= $goods->pk(); ?>)" href="javascript:;">
                            <i class="glyphicon glyphicon-shopping-cart"></i> Добавить в корзину
                        </a>
                    <? else: ?>
                        <a class="b-btn-product-order" onclick="add_cart(<?= $goods->pk(); ?>)" href="javascript:;">
                            Заказать
                        </a>
                    <? endif; ?>

                    <? if ($goods->is_amount && ($goods->getPrice(false, false) >= PRICE_CREDIT)): ?>

                        <a class="b-btn-product-credit" data-popup="#credit_popup_form" href="javascript:;">
                            Хочу в кредит
                        </a>

                    <? endif ?>

                    <? if ($goods->is_amount): ?>
                        <a onclick="one_click(<?= $goods->pk(); ?>)"
                           class="b-btn-product-fast-order"
                           href="javascript:;">
                            Купить в 1 клик
                        </a>
                    <? endif; ?>

                    <div class="b-product__gift">
                        <? if ($goods->is_amount): ?>
                            <? if ($goods->getPrice(false, false) >= PRICE_DELIVERY_FREE): ?>
                                Бесплатная доставка по Екатеринбургу
                            <? else: ?>
                                Бесплатная доставка по Екатеринбургу при сумме заказа от <?= PRICE_DELIVERY_FREE ?> р.
                            <? endif; ?>
                        <? else: ?>
                            Доставим в течение <?= $goods->delivery ? $goods->delivery : 12 ?> дней
                        <? endif; ?>
                    </div>

                </div>
            </div>

            <?= $this->action('Shop:Commodity:Goods:accompanies', array(
                'goodsId' => $goods->pk(),
//                'type_id' => \Shop\Commodity\Entity\Accompany::TYPE_OTHER
            )); ?>

            <div class="b-sections">
                <nav class="b-sections__nav">
                    <? if ($count_chars != 0 || $vendor): ?>
                        <a class="active" href="#chars">Характеристики</a>
                    <? endif; ?>

                    <? foreach ($sections as $key => $item): ?>
                        <a class="<?= ($key == 0 && ($count_chars == 0 && !$vendor)) ? 'active' : '' ?>"
                           href="#sec<?= $item->pk(); ?>"><?= $item->name ?></a>
                    <? endforeach; ?>

                    <? if ($count_attrs != 0): ?>
                        <a href="#grade">Комплектация</a>
                    <? endif; ?>
                </nav>
                <div class="b-sections__layout">
                    <? if ($count_chars != 0 || $vendor): ?>
                        <div class="b-section__item b-attributes active" id="chars">
                            <table class="table table-hover table-condensed">

                                <? if ($vendor): ?>
                                    <tr class="b-attribute__item / identifier /"
                                        itemprop="additionalProperty" itemscope
                                        itemtype="http://schema.org/PropertyValue">
                                        <td class=" / type /" itemprop="name">
                                            Производитель
                                        </td>
                                        <td class=" / value / " itemprop="value">
                                            <?= $vendor; ?>
                                        </td>
                                    </tr>
                                <? endif; ?>

                                <? foreach ($chars as $key => $char): ?>
                                    <? foreach ($char as $item): ?>
                                        <tr class="b-attribute__item / identifier /"
                                            itemprop="additionalProperty" itemscope
                                            itemtype="http://schema.org/PropertyValue">
                                            <td class=" / type /" itemprop="name">
                                                <?= $item['chara']['name'] ?>
                                            </td>
                                            <td class=" / value / ">

                                                <? if (count($item['values'])): ?>
                                                    <? foreach ($item['values'] as $key => $value): ?>
                                                        <?= $key == 0 ? '' : ',' ?>
                                                        <span
                                                                itemprop="value"><?= $value['name'] ?> <?= $value['unit'] ?></span>
                                                    <? endforeach; ?>
                                                <? else: ?>
                                                    <span
                                                            itemprop="value"><?= $item['value']['name'] ?> <?= $item['value']['unit'] ?></span>
                                                <? endif; ?>

                                            </td>
                                        </tr>
                                    <? endforeach; ?>
                                <? endforeach; ?>
                            </table>
                        </div>


                    <? endif; ?>

                    <? foreach ($sections as $key => $item): ?>
                        <div
                                class="b-section__item b-section__text b-text
                            <?= ($key == 0 && ($count_chars == 0 && !$vendor)) ? 'active' : '' ?> "
                            <?= ($key == 0) ? ' itemprop="description"' : '' ?>
                                id="sec<?= $item->pk() ?>">
                            <?= $item->text ?>
                        </div>
                    <? endforeach; ?>

                    <? if ($count_attrs != 0): ?>
                        <div class="b-section__item b-attributes" id="grade">
                            <table class="table table-hover table-condensed">
                                <? foreach ($attrs as $attr): ?>

                                    <tr class="b-attribute__item / identifier /">
                                        <td class=" / type /">
                                            <?= $attr->name ?>
                                        </td>
                                        <td class=" / value / ">
                                            <?= $attr->value ?>
                                        </td>
                                    </tr>

                                <? endforeach; ?>
                            </table>
                        </div>
                    <? endif; ?>


                    <? if (count($sections) == 0): ?>
                        <span class="meta / description" itemprop="description">
                            <? if ($goods->brief): ?>
                                <?= $goods->brief ?>
                            <? else: ?>
                                <?= $goods->name ?>
                            <? endif; ?>
                        </span>
                    <? endif; ?>

                </div>
            </div>

            <?= $this->action('Boat:Store:Schema:right', array('pid' => $goods->pk())) ?>


        </div>
        <div class="b-product__right">

            <div class="b-product__informer">
                <div class="b-product__informer-item">
                    <div class="b-product__informer-name">
                        Сервисный центр:
                    </div>
                    <div class="b-product__informer-text">Обслуживание, диагностика и ремонт лодок и лодочных моторов.</div>
                </div>

                <div class="b-product__informer-item">
                    <div class="b-product__informer-name">
                        Производство:
                    </div>
                    <div class="b-product__informer-text">Изготовление полов к надувным лодкам, пошив тентов, оснащение лодок и катеров дистанционным
                        управлением
                    </div>
                </div>

                <div class="b-product__informer-item">
                    <div class="b-product__informer-name">
                        Заказ запчастей:
                    </div>
                    <div class="b-product__informer-text">Мы не принимаем заказы на запчасти от лодочных моторов Selva, ZongSheng и любые отечественные. На все
                        остальные лодочные моторы, если этих запчастей нет на нашем сайте, можете заказать их любым способом
                        связавшись с нами.
                    </div>
                </div>

<!--                <div class="b-product__informer-item">-->
<!--                    <div class="b-product__informer-name">-->
<!--                        Заказ запчастей-->
<!--                    </div>-->
<!--                    <div class="b-product__informer-text">-->
<!--                        Если Вы не нашли то, что необходимо на сайте, просто напишите нам-->
<!--                    </div>-->
<!--                </div>-->

            </div>


            <?= $this->action('Shop:Commodity:Goods:inRnd', array(
                'goodsId' => $goods->pk(),
                'catId' => $goods->cid,
                'limit' => 6
            )); ?>


        </div>
    </div>
</article>

<script type="text/javascript">

    var __product = {
        "id": "<?= $goods->pk()?>",
        "name": '<?= $goods->name?>',
        "price": <?= $goods->getPrice(false, false)?>,
        "brand": '<?= $goods->getVendor(); ?>',
        "category": '<?= $goods->getCategoriesStr() ?>'
    };

    $(function () {
        dataLayer.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "detail": {
                    "products": [__product]
                }
            }
        });
    });
</script>