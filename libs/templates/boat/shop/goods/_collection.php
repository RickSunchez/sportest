<? if (count($collections)): ?>
    <? foreach ($collections as $collection): ?>
        <? if (count($items[$collection['id']])): ?>
            <section class="b-product__option-item">
                <header class="b-product__option-header">
                    <h3 class="b-product__option-title">
                        <?= $collection['label'] ?>:
                    </h3>
                </header>
                <div class="b-product__option-list">
                    <? foreach ($items[$collection['id']] as $item): ?>
                        <? if (isset($products[$item['product_id']])): ?>
                            <?
                            $product = \Shop\Commodity\Entity\Goods::mock($products[$item['product_id']]);
                            $active = ($currentId == $product->pk());
                            ?>
                            <a href="<?= $active ? 'javascript:;' : $product->link(); ?>"
                               title="<?= $this->escape($product->name); ?>"
                               class="b-select-option_checkbox <?= $active ? 'active' : '' ?>">
                                <?= $item['name']; ?>

                            </a>
                        <? endif; ?>
                    <? endforeach; ?>
                </div>
            </section>
        <? endif; ?>
    <? endforeach; ?>
<? endif; ?>

