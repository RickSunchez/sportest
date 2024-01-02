<? if (count($options)): ?>
    <section id="options_additions_<?= $goods_id ?>" class="b-product__options-additions">

        <? foreach ($options as $opt): ?>
            <? if (isset($variants[$opt->pk()])): ?>
                <div class="b-product__options-additions__item">
                    <label><?= $opt->name ?>:</label>
                    <?= $this->action('Shop:Commodity:Option:select', array(
                        'prefix' => 'additions',
                        'option' => $opt,
                        'variants' => new \ArrayObject($variants[$opt->pk()])
                    )); ?>
                </div>
            <? endif; ?>
        <? endforeach ?>

    </section>
    <script type="text/javascript">
        $('#product_text_<?= $goods_id?>').remove();
    </script>
<? endif; ?>
