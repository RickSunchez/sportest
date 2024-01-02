<article class="b-page__content">
    <div class="b-page-show__text">
        <h1 class="b-page__title"><?= $goods->name; ?></h1>
        <? if ($goods->article): ?>
            <div class="b-product__article identifier">
                <span class="type name">Артикул:</span>
                <span class="value"><?= $goods->article ?></span>
            </div>
        <? endif; ?>


        <div id="sections" class="b-sections">
            <section class="b-sections__item">
                <h2 class="b-sections__name ">Товаров по вашему запросу больше не продается</h2>
                <section class="b-sections__text b-text">
                    <br/>

                    <p><b>Для успешного поиска:</b></p>
                    <ul>
                        <li>Посмотреть похожий товар</li>
                        <li>Воспользоваться формой поиска</li>
                        <li>Перейдите на <a href="/">главную</a> страницу</li>
                    </ul>


                </section>
            </section>

        </div>


        <footer class="m-goods__footer">

            <?= $this->action('Shop:Commodity:Goods:inRnd', array(
                'goodsId' => $goods->pk(),
                'catId' => $goods->cid,
                'limit' => 6
            )); ?>


        </footer>

    </div>
</article>