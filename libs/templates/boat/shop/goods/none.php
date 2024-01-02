<article class="b-goods">

    <header class="b-goods__header">
        <h1 class="b-goods__title"><?= $goods->name ?></h1>
        <? if ($goods->article): ?>
            <div class="b-goods__article">
                <span class="type">Артикул: </span>
                <span class="value"><?= $goods->article ?></span>
            </div>
        <? endif; ?>
    </header>

    <section class="b-goods__body b-goods__body_empty b-text">
        <h2>Товаров по вашему запросу больше не продается</h2>
        <br/>
        <p><b>Для успешного поиска:</b></p>
        <ul>
            <? if ($category): ?>
                <li>Перейти в текущую категории:
                    <a href="<?= link_to_city('shop_category_list', array('cid' => $category->pk(), 'url' => $category->url)) ?>"><?= $category->name ?></a>
                </li>
            <? endif; ?>
            <li>Посмотреть похожий товар</li>
            <li>Воспользоваться формой поиска</li>
        </ul>


    </section>

    <footer class="b-goods__footer">

        <?= $this->action('Shop:Commodity:Goods:inRnd', array(
            'goodsId' => $goods->pk(),
            'catId' => $goods->cid,
            'limit' => 7
        )); ?>

    </footer>
</article>
