<? if (count($lines)): ?>
    <? foreach ($lines as $line): ?>
        <section class="b-picking">
            <h3 class="b-picking__title"><?= $line->name ?></h3>
            <?= $this->action('Shop:Catalog:Shop:linesItems', array('line' => $line)) ?>
            <? if ($line->url): ?>
                <div class="b-picking__link">
                    <a title="Перейти" class="b-picking__btn"
                       href="<?= $line->url ?>"><?= $line->btn ? $line->btn : 'Подробнее' ?>
                    </a>
                </div>
            <? endif; ?>
        </section>
    <? endforeach; ?>
<? endif; ?>
