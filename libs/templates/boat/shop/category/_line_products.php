<? if (count($lines)): ?>
    <? foreach ($lines as $line): ?>
        <section class="b-products-line">
            <h3 class="b-products-line__title"><?= $line->name ?></h3>
            <?= $this->action('Shop:Catalog:Shop:linesItems', array('line' => $line)) ?>
            <? if ($line->url): ?>
                <div class="b-products-line__link">
                    <a title="Перейти" class="b-products-line__btn"
                       href="<?= $line->url ?>"><?= $line->btn ? $line->btn : 'Подробнее' ?>
                    </a>
                </div>
            <? endif; ?>
        </section>
    <? endforeach; ?>
<? endif; ?>
