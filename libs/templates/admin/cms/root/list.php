<div class="clearfix">
    <a class="btn btn-info btn-xs" href="<?= link_to('admin_root', array('action' => 'add')) ?>">Добавить
        пользователя</a>
</div>
<br/>
<table class="table table-condensed table-bordered table-hover">
    <tr>
        <th width="20">ID</th>
        <th>Login</th>
        <th width="50">Создан</th>
        <th width="20">#</th>
    </tr>
    <? foreach ($users as $user): ?>
        <tr>
            <td><?= $user->pk(); ?></td>
            <td><?= $user->login ?></td>
            <td><?= date('d-m-Y', $user->date_cr) ?></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li><a title="Редактировать"
                               href="<?= link_to('admin_root', array('id' => $user->pk(), 'action' => 'edit')) ?>"><i
                                    class="icon-edit"></i> Редактировать</a></li>
                        <li>
                            <a title="Удалить" onclick="return confirm('Удалить?');"
                               href="<?= link_to('admin_root', array('id' => $user->pk(), 'action' => 'delete')) ?>"><i
                                    class="icon-trash"></i> Удалить</a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    <? endforeach; ?>
</table>