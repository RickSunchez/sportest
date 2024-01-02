<article class="b-category" itemscope itemtype="http://schema.org/ItemList">
    <meta itemprop="numberOfItems" content="<?= $pagination->getItemCount(); ?>">
    <div class="b-table">

        <div class="b-table-cell l-product__content l-product__content_none-filter ">

            <header class="b-category__header">
                <h1 itemprop="name" class="b-category__title">Поиск: <?= \Delorius\Utils\Strings::lower($query) ?></h1>
            </header>

            <? if ($pagination->getItemCount() != 0): ?>

                <div class="b-well">
                    По данному запросу найдено <strong><?= $pagination->getItemCount(); ?>
                        <?= \Delorius\Utils\Strings::pluralForm($pagination->getItemCount(), 'товар', 'товара', 'товаров') ?></strong>.

                </div>

            <? else: ?>
                <div class="b-well">
                    По данному запросу ничего не найдено. Попробуйте поменять запрос или воспользуйтель каталогом.
                </div>
            <? endif; ?>


            <? if (count($goods)): ?>

                <?= $this->action('Shop:Commodity:Goods:filterSort'); ?>

                <div class="b-products / hListing">
                    <? foreach ($goods as $item): ?>
                        <section data-id="<?= $item->pk(); ?>" id="product_<?= $item->pk(); ?>"
                                 class="b-goods__item / item hproduct /"
                                 itemprop="itemListElement" itemscope itemtype="http://schema.org/Product">
                            <div class="b-goods__item_layout">

                                <meta itemprop="productID" content="<?= $item->pk(); ?>">
                                <meta itemprop="category" content="<?= product_cat($item->cid); ?>">
                                <meta itemprop="description" content="<?= $this->escape($item->name); ?>">

                                <figure class="b-goods__link_image" data-href="<?= $item->link(); ?>">


                                    <? if ($item->image): ?>
                                        <meta itemprop="image"
                                              content="<?= CMS\Core\Helper\Helpers::canonicalUrl($item->image->normal) ?>">

                                        <img class="photo" src="<?= $item->image->preview ?>"
                                             alt="<?= $this->escape($item->name) ?>">

                                    <? else: ?>
                                        <img src="/source/images/no.png" alt="">
                                    <? endif; ?>

                                    <? if ($per = $item->getPerDiscount()): ?>
                                        <div class="b-goods__disc" title="Скидка <?= $per ?>%">
                                            <?= $per; ?> %
                                        </div>
                                    <? endif; ?>
                                    <? if ($item->is_amount == 0): ?>
                                        <div class="b-goods__none-text">
                                            Отсуствует на складе
                                        </div>
                                    <? endif; ?>
                                </figure>

                                <div class="b-goods__cat">
                                    <?= product_cat($item->cid); ?>
                                </div>

                                <h2 class="b-goods__title <?= $item->getPerDiscount() != 0 ? 'b-goods__title_disc' : '' ?>">
                                    <a class="b-goods__link / url /" itemprop="url" href="<?= $item->link(); ?>"
                                       title="<?= $this->escape($item->name); ?>">
                                        <span class="fn" itemprop="name"><?= $item->getShortName(); ?></span>
                                    </a>
                                </h2>

                                <div itemprop="offers" itemscope
                                     itemtype="http://schema.org/Offer"
                                     class="b-goods__offer">
                                    <meta itemprop="price" class="price"
                                          content="<?= $item->getPrice(false, false) ?>"/>
                                    <meta itemprop="priceCurrency" content="<?= $item->code ?>"/>
                                    <div class="b-goods__offer-price"><?= $item->getPrice() ?></div>
                                    <? if ($item->getPerDiscount()): ?>
                                        <div class="b-goods__offer-old">
                                            <?= $item->getPriceOld() ?>
                                        </div>
                                    <? endif; ?>
                                </div>

                                <div class="b-goods__misc">
                                    <div class="b-goods__misc-layout">
                                        <? if ($item->is_amount): ?>

                                            <a data-id="<?= $item->pk() ?>"
                                               onclick="df.shop.btnAddCart(
                                            this,
                                            'b-goods__btn--inner',
                                            'b-loading-ajax_status_loading_product',
                                            null,
                                            update_cart
                                            );return false;"
                                               class="b-goods__btn <?= $basket->getQuantity($item->combination_hash) != 0 ? 'b-goods__btn--inner' : '' ?>"
                                               href="javascript:;">
                                                В корзину
                                            </a>

                                        <? else: ?>

                                            <a data-id="<?= $item->pk() ?>"
                                               onclick="df.shop.btnAddCart(
                                            this,
                                            'b-goods__btn--inner',
                                            'b-loading-ajax_status_loading_product',
                                            null,
                                            update_cart
                                            );return false;"
                                               class="b-goods__btn-order <?= $basket->getQuantity($item->combination_hash) != 0 ? 'b-goods__btn--inner' : '' ?>"
                                               href="javascript:;">
                                                Заказать
                                            </a>
                                            <div class="b-goods__delivery">Доставим в течение 12 дней</div>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <? endforeach; ?>
                </div>


            <? if ($pagination->getItemCount() != 0): ?>
            <?= $pagination; ?>


                <script type="text/javascript">
                    $(function () {
                        var search = new HR(".fn", {
                            highlight: <?=\Delorius\Utils\Json::encode((array)$aQuery);?>,
                            backgroundColor: "#c4f5aa"
                        });
                        search.hr();
                    });

                </script>


            <? endif; ?>

            <? endif; ?>

        </div>
    </div>
</article>