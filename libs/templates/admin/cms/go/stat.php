<div class="clearfix">
    <a class="btn btn-danger btn-xs" href="<?= link_to('admin_go',array('action'=>'list')) ?>">Назад</a>
</div>
<br />
<div>
    <div>Кол-во записей: <?= $pagination->getItemCount() ?></div>
</div>

<table class="table table-condensed table-bordered table-hover" >
    <tr>
        <th>IP</th>
        <th>Reffer</th>
        <th>Domain</th>
        <th>Email</th>
        <th>Date</th>
    </tr>
    <?foreach($stats as $item):?>
        <tr>
            <td><?= $item->ip;?></td>
            <td><?= $item->url_ref?></td>
            <td><?= $item->domain?></td>
            <td><?= $item->is_mail ? "Да": ""?></td>
            <td><?= date('d.m.Y H:i',$item->date_cr)?></td>
        </tr>
    <?endforeach;?>
</table>

<?= $pagination->render(); ?>