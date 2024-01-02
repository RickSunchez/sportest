<article class="b-page__content">
    <header>
        <h1 class="b-page__title"><?= $category->getHeaderTitle(); ?></h1>
    </header>
    <? if (count($goods)): ?>

        <?= $this->action('Boat:Store:Schema:note', array('cid' => $category->pk())) ?>


        <aside class="l-nav-sort">
            <div class="b-table">
                <div class="b-table-cell l-nav-sort__btn">
                    <div data-open="sort" class="b-sort__btn"></div>
                </div>
                <div class="b-table-cell l-nav-sort__categories">
                    <div data-open="categories" class="b-category-model__btn">
                        Категории ↓
                    </div>
                </div>
            </div>
        </aside>
        <?= $this->action('Shop:Catalog:Shop:sub', array('categoryId' => $category->pk(), 'theme' => 'model', 'image' => true)); ?>
        <?= $this->action('Shop:Commodity:Goods:filterSort') ?>

        <ul class="b-goods-list__layout hListing">

            <? foreach ($goods as $item): ?>
                <li class="b-goods-item b-goods-item_<?= $item->pk() ?>"
                    id="goods_<?= $item->pk() ?> item hproduct">

                    <?= $this->partial('shop/goods/_item_horiz', array(
                        'goods' => $item,
                        'basket' => $basket
                    )) ?>

                </li>
            <? endforeach ?>

        </ul>

        <?= $pagination->render(); ?>

    <? else: ?>
        <?= $this->action('Shop:Catalog:Shop:sub', array('categoryId' => $category->pk())); ?>
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
                    <li>★ Быстрая и бережная доставка в любую точку <?= snippet('city', 'name', array('v' => 3)) ?></li>
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


</article>