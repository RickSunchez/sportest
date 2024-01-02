<div style="margin-bottom: 20px"><b>Характиристика:</b> <?= $chara->name ?> = <?= $value->name ?></div>

<table class="table table-condensed table-bordered table-hover">
    <tr>
        <th width="20">ID</th>
        <th width="20">State</th>
        <th>Название</th>
        <th>Категория</th>
    </tr>
    <? foreach ($goods as $item): ?>
        <tr>
            <td><?= $item->pk() ?></td>
            <td><?= $item->status?'On':'Off' ?></td>
            <td><a target="_blank" href="<?= $item->link() ?>"><?= $item->name ?></a></td>
            <td><?= $item->getCategoriesStr() ?></td>
        </tr>
    <? endforeach; ?>

</table>



