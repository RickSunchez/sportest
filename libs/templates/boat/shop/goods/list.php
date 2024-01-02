<article class="b-category" itemscope itemtype="http://schema.org/ItemList">
    <meta itemprop="numberOfItems" content="<?= $pagination->getItemCount(); ?>">
    <div class="b-table">
        <div class="b-table-cell l-product__content">

            <header class="b-category__header">
                <h1 itemprop="name" class="b-category__title"><?= $category->getHeaderTitle() ?></h1>
            </header>

            <?= $this->action('Boat:Store:Schema:note', array('cid' => $category->pk())) ?>

            <? if ($category instanceof \Shop\Catalog\Entity\Category): ?>
                <?= $this->action('Shop:Catalog:Shop:sub', array('categoryId' => $category->pk())) ?>
            <? endif; ?>

            <? if ($category && $category->text_top && $pagination->getPage() == 1): ?>
                <section class="b-category__text b-category__text_top b-text">
                    <?= $category->text_top; ?>
                </section>
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

                                <a class="b-goods__link_image"
                                   title="<?= $this->escape($item->name); ?>"
                                   href="<?= $item->link(); ?>">

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
                                           - <?= $per; ?> %
                                        </div>
                                    <? endif; ?>
                                    <? if ($item->is_amount == 0): ?>
                                        <div class="b-goods__none-text">
                                            Отсуствует на складе
                                        </div>
                                    <? endif; ?>
                                </a>

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
                                                <i class="glyphicon glyphicon-shopping-cart"></i> В корзину
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

                <?= $pagination ?>

            <? endif; ?>

            <? if ($pagination->getPage() == 1): ?>
                <? if ($category && $category->text_below): ?>
                    <section class="b-category__text b-category__text_below b-text">
                        <?= $category->text_below; ?>
                    </section>
                <? elseif ($category): ?>
                    <section class="b-category__text b-category__text_below b-text">
                        <p>✔ Интернет-магазин «СпортЕсть.Ру» это:</p>
                        <ul>
                            <? if ($category instanceof \Shop\Catalog\Entity\Category): ?>
                                <li>
                                    ★ (<?= $pagination->getItemCount() ?>) <?= $category->getHeaderTitle() ?>
                                    по ценам от <?= snippet('shop', 'min_price') ?>
                                </li>
                            <?else:?>
                                <li>
                                    ★ (<?= $pagination->getItemCount() ?>) <?= $category->getHeaderTitle() ?>
                                </li>
                            <? endif; ?>
                            <li>★ Быстрая и бережная доставка в любую
                                точку <?= snippet('city', 'name', array('v' => 3)) ?></li>
                            <li>★ <?= $category->getHeaderTitle() ?> от производителей в рассрочку или кредит.</li>
                        </ul>
                        <p>Сразу после оформления заказа, наши менеджеры свяжутся с Вами, чтобы уточнить характеристики
                            выбранного товара, дату, место и время доставки. Для наших клиентов предусмотрены следующие
                            формы оплаты: </p>
                        <ul>
                            <li>Наличный расчет в магазине</li>
                            <li>Картой Visa/MasterCard</li>
                            <li>Онлайн оплата</li>
                        </ul>
                    </section>
                <? endif; ?>
            <? endif; ?>

        </div>
        <div class="b-table-cell l-product__filter">

            <?= $this->action('Shop:Catalog:Filters:list') ?>

        </div>
    </div>
</article>