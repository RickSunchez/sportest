<div >
    <div class="clearfix">
        <a class="btn btn-info btn-xs"  href="<?= link_to('admin_delivery',array('action'=>'add'))?>" >Добавить рассылку</a>
    </div>
    <br clear="all" />




    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th>Темы</th>
            <th>Статус</th>
            <th>Запущена</th>
            <th>Завершина</th>
            <th width="20">#</th>
        </tr>
        <?foreach($delivery as $item):?>
        <tr >
            <td><?= $item->subject?></td>
            <td><?= $item->status? "ВКЛ": "ВЫКЛ" ;?></td>
            <td><?= $item->started? date('d/m/Y H:i',$item->date_start): "НЕТ" ;?></td>
            <td><?= $item->finished? date('d/m/Y H:i',$item->date_end): "НЕТ" ;?></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li><a tabindex="-1" href="<?= link_to('admin_delivery',array('action'=>'edit','id'=>$item->pk()))?>" >Редактировать</a></li>
                        <li><a tabindex="-1" href="<?= link_to('admin_delivery',array('action'=>'delete','id'=>$item->pk()))?>" >Удалить</a></li>
                    </ul>
                </div>
            </td>
        </tr>
        <?endforeach;?>
    </table>
</div>
