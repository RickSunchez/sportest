<?php
    use Shop\Components\Import1C\interfaces\IImport1C;
?>

<table class="table table-condensed table-bordered table-hover">

    <tr>
        <th>#</th>
        <th>Дата</th>
        <th>Статус</th>
        <th>Сообщения</th>
    </tr>

    <?php foreach ($list as $i => $item) : ?>

        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= $item->datetime ?></td>
            <td>
                <?php switch ($item->status):
                    case IImport1C::STATUS_ERROR: ?>
                        <span class="btn btn-xs btn-danger">
                            <i class="glyphicon glyphicon-remove"></i>
                        </span>
                        <?php break; 
                    case IImport1C::STATUS_SUCCESS: ?>
                        <span class="btn btn-xs btn-success">
                            <i class="glyphicon glyphicon-ok"></i>
                        </span>
                        <?php break; ?>
                <?php endswitch ?>
            </td>
            <td>
                <?php foreach ($item->statusMessages as $message) : ?>
                    <p><?= $message ?></p>
                <?php endforeach ?>
            </td>
        </tr>

    <?php endforeach ?>

</table>
