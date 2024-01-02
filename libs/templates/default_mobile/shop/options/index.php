<? if (count($options)): ?>
    <section id="options" class="b-product__options">
        <div class="b-well">Выберите желаемые опции</div>
        <? foreach ($options as $opt): ?>
            <? if (isset($variants[$opt->pk()])): ?>
                <div class="b-product__options-item">
                    <label><?= $opt->name ?>:</label>

                    <div class="b-addition__options">
                        <?= $this->action('Shop:Commodity:Option:select', array(
                            'option' => $opt,
                            'variants' => new \ArrayObject($variants[$opt->pk()])
                        )); ?>
                    </div>
                </div>
            <? endif; ?>
        <? endforeach ?>
    </section>
<? endif; ?>
