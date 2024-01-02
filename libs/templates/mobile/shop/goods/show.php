<? $attrs = $goods->getAttributes(); ?>
<article class="b-product b-page__content b-goods / item hproduct /" data-id="<?= $goods->pk(); ?>"
         id="product_<?= $goods->pk(); ?>"
         itemscope itemtype="http://schema.org/Product">

    <meta itemprop="productID" content="<?= $goods->pk(); ?>">
    <span class="category meta" itemprop="category"><?= product_cat($goods->cid) ?></span>

    <div class="b-page-show__text">

        <h1 class="b-page__title b-product__name"><?= $goods->name; ?></h1>

        <? if ($per = $goods->getPerDiscount()): ?>
            <div class="b-product__disc" title="Скидка <?= $per ?>%">
                Скидка <?= $per; ?> %
            </div>
        <? endif; ?>

        <div class="b-product__image js-product-carousel">
            <? if (count($images)): ?>
                <? foreach ($images as $image): ?>
                    <div class="item">
                        <img class="photo" src="/thumb/300/<?= $image->pk() ?>" alt="<?= $image->name ?>">
                    </div>
                <? endforeach; ?>
            <? endif; ?>

        </div>


        <div class="b-product__price"
             itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <span itemprop="price" class=" meta price"><?= $goods->getPrice(false, false) ?></span>
            <span itemprop="priceCurrency" class=" meta currency"><?= $goods->code ?></span>
           <span class="name">
                Цена:
            </span>
            <span class="price init-price">
                 <?= $goods->getPrice() ?>
            </span>

            <? if ($goods->getPerDiscount()): ?>
                <span class="discount"><?= $goods->getPriceOld(); ?></span>
            <? endif; ?>
        </div>

        <?= $this->action('Shop:Commodity:Goods:collectionProduct'); ?>
        <div class="b-product__line-btn">
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
        </div>
        <? if ($goods->is_amount && ($goods->getPrice(false, false) >= PRICE_CREDIT)): ?>
            <div class="b-product__line-btn">
                <a class="b-btn-product-credit" data-open="credit_form" href="javascript:;">
                    Хочу в кредит
                </a>
            </div>

            <script>

                var __options = {
                    operId: uuidv4(),
                    productCode: 'EXP_MP_PP_23,9',
                    ttCode: '0601001014351',
                    toCode: '060100101435',
                    ttName: '620062, г. Екатеринбург, ул. Гагарина, д. 10',
                    brokerAgentId: 'NON_BROKER',
                    firstPayment: '',
                    returnUrl: 'https://www.sportest.ru',
                    fullName: '',
                    phone: '',
                    order: [{
                        category: "262",
                        mark: "Товары для рыбной ловли",
                        model: "Товары для рыбной ловли",
                        quantity: "1",
                        price: 0,
                    }]
                };

            </script>

        <? endif ?>

        <? if ($goods->is_amount): ?>
            <div class="b-product__line-btn">
                <a onclick="one_click(<?= $goods->pk(); ?>)"
                   class="b-btn-product-fast-order"
                   href="javascript:;">
                    Купить в 1 клик
                </a>
            </div>
        <? endif; ?>


        <?= $this->action('Boat:Store:Schema:li', array('pid' => $goods->pk())) ?>

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


        <div id="sections" class="b-sections">
            <? if ($chars = $goods->getGroupCharacteristics()): ?>
                <section class="b-sections__item">
                    <h2 class="b-sections__name">Характеристики:</h2>
                    <section class="b-sections__text">
                        <ul class="b-characteristics">
                            <? foreach ($chars as $char): ?>
                                <? foreach ($char as $characteristic): ?>
                                    <li class="identifier">
                                        <span class="type"><?= $characteristic['chara']['name'] ?>:</span>
                                        <span class="value">
                                             <? if (count($characteristic['values'])): ?>
                                                 <? foreach ($characteristic['values'] as $key => $value): ?>
                                                     <?= $key == 0 ? '' : ',' ?>
                                                     <?= $value['name'] ?> <?= $value['unit'] ?>
                                                 <? endforeach; ?>
                                             <? else: ?>
                                                 <?= $characteristic['value']['name'] ?> <?= $characteristic['value']['unit'] ?>
                                             <? endif; ?>

                                        </span>
                                    </li>
                                <? endforeach ?>
                            <? endforeach ?>
                        </ul>
                    </section>
                </section>
            <? endif ?>

            <? if (count($sections)): ?>
                <? foreach ($sections as $key => $section): ?>

                    <? if ($section->text): ?>
                        <section class="b-sections__item">
                            <h2 class="b-sections__name "><?= $section->name; ?></h2>
                            <section
                                class="b-sections__text b-text <?= $key == 0 ? ' / description /" itemprop="description' : '' ?>">

                                <?= $section->text; ?>
                            </section>
                        </section>
                    <? endif ?>
                <? endforeach ?>
            <? else: ?>
                <span class="meta / description" itemprop="description">
                            <? if ($goods->brief): ?>
                                <?= $goods->brief ?>
                            <? else: ?>
                                <?= $goods->name ?>
                            <? endif; ?>
                        </span>
            <? endif; ?>


        </div>

        <?= $this->action('Boat:Store:Schema:right', array('pid' => $goods->pk())) ?>


        <footer class="m-goods__footer">

            <?= $this->action('Shop:Commodity:Goods:accompanies', array(
                'goodsId' => $goods->pk(),
//                'type_id' => \Shop\Commodity\Entity\Accompany::TYPE_OTHER
            )); ?>


            <?= $this->action('Shop:Commodity:Goods:inRnd', array(
                'goodsId' => $goods->pk(),
                'catId' => $goods->cid,
                'limit' => 6
            )); ?>

            <?= $this->action('Shop:Commodity:Goods:youWatched'); ?>

        </footer>

    </div>
</article>

<style>
    .breadcrumb li.active {
        display: none;
    }
</style>

<script type="text/javascript">
    $(function () {
        $('.b-sections__name').addClass('b-sections__name--close');
    });
</script>

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