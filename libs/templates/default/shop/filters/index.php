<form action="<?= $url ?>" method="post" class="b-filters">
    <? foreach ($filters as $filter): ?>
        <? if ($filter->type_id == \Shop\Catalog\Entity\Filter::TYPE_CATEGORY): ?>
            <?= $this->action('Shop:Catalog:Filters:category', array('category' => $category, 'filter' => $filter)); ?>
        <? elseif ($filter->type_id == \Shop\Catalog\Entity\Filter::TYPE_GOODS): ?>
            <?= $this->action('Shop:Catalog:Filters:goodsParams', array('category' => $category, 'filter' => $filter)); ?>
        <?
        elseif ($filter->type_id == \Shop\Catalog\Entity\Filter::TYPE_FEATURE): ?>
            <?= $this->action('Shop:Catalog:Filters:feature', array('category' => $category, 'filter' => $filter)); ?>
        <? endif; ?>
    <? endforeach; ?>
    <button class="b-filters__submit" type="submit">Сортировать</button>
</form>