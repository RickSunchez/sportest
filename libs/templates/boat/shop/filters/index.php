<form
    data-cid="<?= DI()->getService('site')->categoryId;?>"
    data-col-cid="<?= DI()->getService('site')->collectionCategoryId; ?>"
    data-city-id="<?= city_builder()->getId(); ?>"
    action="<?= $url ?>"
    method="get"
    class="b-filters">
    <div class="b-filters__title">Фильтр</div>
    <div>
        <? foreach ($filters as $filter): ?>
        <? $filter = \Shop\Catalog\Entity\Filter::mock($filter)?>
            <? if ($filter->type_id == \Shop\Catalog\Entity\Filter::TYPE_CATEGORY): ?>
                <?= $this->action('Shop:Catalog:Filters:category', array('filter' => $filter)); ?>
            <? elseif ($filter->type_id == \Shop\Catalog\Entity\Filter::TYPE_GOODS): ?>
                <?= $this->action('Shop:Catalog:Filters:goodsParams', array('filter' => $filter)); ?>
            <? elseif ($filter->type_id == \Shop\Catalog\Entity\Filter::TYPE_FEATURE): ?>
                <?= $this->action('Shop:Catalog:Filters:feature', array('filter' => $filter)); ?>
            <? elseif ($filter->type_id == \Shop\Catalog\Entity\Filter::TYPE_COLLECTION_MAIN): ?>
                <?= $this->action('Shop:Catalog:Filters:collectionMain', array('filter' => $filter)); ?>
            <? elseif ($filter->type_id == \Shop\Catalog\Entity\Filter::TYPE_COLLECTION): ?>
                <? $collection = $filter; ?>
            <? endif; ?>
        <? endforeach; ?>
    </div>
    <button class="b-filters__submit" type="submit">Сортировать</button>

    <? if (count($get)): ?>
        <a class="b-filters__clean" data-href="<?= $url ?>">Очисть фильтр</a>
    <? endif; ?>

    <? if ($collection): ?>
        <?= $this->action('Shop:Catalog:Filters:collection', array('filter' => $collection)); ?>
    <? endif; ?>
</form>

<script>
    $('.b-filter-item__checkbox:checked').each(function (index, item) {

        var filter = $(item).data('filterId');
        $('#sr-feature-' + filter).prop('checked', true);

    });
</script>
