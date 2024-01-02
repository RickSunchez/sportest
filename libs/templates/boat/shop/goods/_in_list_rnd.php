<? if (count($goods)): ?>
    <section class="b-product__horz">
        <header class="b-product__horz-header">
            <h2>Похожие товары</h2>
        </header>

        <ul class="b-product-horz__layout">
            <? foreach ($goods as $item): ?>
                <li class="b-product-horz__item">

                    <div class="b-product-horz__img">
                        <a title="<?= $this->escape($item->name); ?>" class="b-product-horz__link"
                           href="<?= $item->link() ?>">
                            <? if ($item->image): ?>
                                <img src="<?= $item->image->preview; ?>" alt="">
                            <? else: ?>
                                <img src="/source/images/no.png" alt="">
                            <? endif; ?>
                        </a>
                    </div>
                    <div class="b-product-horz__info">
                        <a class="b-product-horz__link" href="<?= $item->link() ?>"><?= $item->name; ?></a>

                        <div class="b-product-horz__price"><?= $item->getPrice() ?></div>

                    </div>

                </li>
            <? endforeach; ?>
        </ul>

    </section>
<? endif; ?>