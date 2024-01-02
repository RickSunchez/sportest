<h1>Таблицы</h1>

<div class="btn-group" style="margin: 10px 0">
    <a class="btn btn-danger btn-xs" href="<?= link_to('admin_migration', array('action' => 'index')) ?>">
        Назад к миграции
    </a>
    <a class="btn btn-success btn-xs" href="<?= link_to('admin_migration', array('action' => 'tableUpdate')) ?>">
        Дополнить таблицу
    </a>
    <a class="btn btn-info btn-xs" href="<?= link_to('admin_migration', array('action' => 'tableUpgrade')) ?>">
        Upgrade "table_name=>id"
    </a>
</div>

<table>
    <tr>
        <th width="30">ID</th>
        <th>Name</th>
    </tr>
    <? foreach ($table as $item): ?>
        <tr>
            <td><?= $item['id'] ?></td>
            <td><?= $item['target_type'] ?></td>
        </tr>
    <? endforeach; ?>
</table>