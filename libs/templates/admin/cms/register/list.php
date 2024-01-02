
<h1>События</h1>
<?if(count($registers)):?>
<form action="<?= link_to('admin_register', array('action' => 'clear')) ?>" method="post">
    <button type="submit" class="btn btn-info btn-xs">Архивировать</button>
</form>
<br />
<table class="table table-bordered table-hover table-condensed">
    <tr>
        <th width="20"></th>
        <th class="i-center-td">Space</th>
        <th>Сообщение</th>
        <th width="130" class="i-center-td">Дата</th>
        <th class="i-center-td">User</th>
        <th class="i-center-td">ORM</th>
        <th class="i-center-td">IP</th>
    </tr>
    <? foreach ($registers as $item): ?>
        <tr class="b-register-type_<?= $item->type ?>">
            <td class="i-center-td">
                <b style="cursor: pointer;">
                    <?= $item->type == 10 ? '<span title="ATTENTION">A</span>' : ''; ?>
                    <?= $item->type == 11 ? '<span title="INFO">I</span>' : ''; ?>
                    <?= $item->type == 12 ? '<span title="ERROR">E</span>' : ''; ?>
                </b>
            </td>
            <td class="i-center-td"><?= $item->getSpaceName(); ?></td>
            <td><?= $item->text ?></td>
            <td class="i-center-td"><?= date('d-m-Y H:i', $item->date_cr) ?></td>
            <td class="i-center-td">
                <? if ($item->user_id): ?>
                    <?= $item->user_namespace ?>::<?= $item->user_id ?>
                <? endif; ?>
            </td>
            <td class="i-center-td">
                <? if ($item->target_id): ?>
                    <?= $item->target_type ?>::<?= $item->target_id ?>
                <? endif; ?>
            </td>
            <td><?= $item->ip ?></td>
        </tr>
    <? endforeach; ?>
</table>

<?= $pagination ?>

<?else:?>
    <p>Нет зарегистрированных событий</p>
<?endif;?>