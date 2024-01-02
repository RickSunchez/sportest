<article class="b-page__content">
    <h1 class="b-page__title">Вы ищите: "<?= $query ?>"</h1>



    <? if ($pagination->getItemCount() == 0): ?>
        <div class="b-well">
            По данному запросу ничего не найдено. Попробуйте поменять запрос или воспользуйтель каталогом.
        </div>
    <? endif; ?>

    <? if (count($goods)): ?>
        <?= $this->action('Shop:Commodity:Goods:filterSort'); ?>
        <aside class="l-nav-sort">
            <div class="b-table">
                <div class="b-table-cell l-nav-sort__btn">
                    <div data-open="sort" class="b-sort__btn"></div>
                </div>
                <div class="b-table-cell l-nav-sort__info">
                    По данному запросу найдено <strong><?= $pagination->getItemCount(); ?>
                        <?= \Delorius\Utils\Strings::pluralForm($pagination->getItemCount(), 'товар', 'товара', 'товаров') ?></strong>.

                </div>
            </div>
        </aside>


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


</article>