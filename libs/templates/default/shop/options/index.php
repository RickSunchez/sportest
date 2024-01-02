<? if (count($options)): ?>
    <form action="<?= link_to('goods_option_data',array('action'=>'changed'))?>" method="get">
        <? foreach ($options as $opt): ?>
            <?if(isset($variants[$opt->pk()])):?>
            <section>
                <header>
                    <h1><?= $opt->name ?></h1>
                </header>


                <? if ($opt->type == \Shop\Commodity\Entity\Options\Item::TYPE_SELECT): ?>
                    <?= $this->action('Shop:Commodity:Option:select', array(
                        'option' => $opt,
                        'variants' => new \ArrayObject($variants[$opt->pk()])
                    )); ?>
                <? elseif ($opt->type == \Shop\Commodity\Entity\Options\Item::TYPE_RADIO): ?>
                    <?= $this->action('Shop:Commodity:Option:radio', array(
                        'option' => $opt,
                        'variants' => new \ArrayObject($variants[$opt->pk()])
                    )); ?>
                <? elseif ($opt->type == \Shop\Commodity\Entity\Options\Item::TYPE_FLAG): ?>
                    <?= $this->action('Shop:Commodity:Option:flag', array(
                        'option' => $opt,
                        'variants' => new \ArrayObject($variants[$opt->pk()])
                    )); ?>
                <? elseif ($opt->type == \Shop\Commodity\Entity\Options\Item::TYPE_TEXT): ?>
                    <?= $this->action('Shop:Commodity:Option:text', array('option' => $opt)); ?>
                <? elseif ($opt->type == \Shop\Commodity\Entity\Options\Item::TYPE_VARCHAR): ?>
                    <?= $this->action('Shop:Commodity:Option:varchar', array('option' => $opt)); ?>
                <? endif ?>


            </section>
        <?endif;?>
        <? endforeach ?>
        <input type="hidden" name="goods_id" value="<?= $goods_id?>">
        <button type="submit">Отправить</button>
    </form>
<? endif; ?>
