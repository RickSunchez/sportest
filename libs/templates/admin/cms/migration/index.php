<h2>ORM</h2>
<div class="btn-group" style="margin: 10px 0">
    <a class="btn btn-success btn-xs" href="<?= link_to('admin_migration', array('action' => 'start')) ?>">
        Запуск миргации
    </a>
    <a class="btn btn-info btn-xs" href="<?= link_to('admin_migration', array('action' => 'table')) ?>">
        Таблицы
    </a>
</div>
<? foreach ($items as $item): ?>
    <div class="b-model">
        <div class="b-model__class" onclick="name_togget('.b-model__<?= $item['table_name'] ?>');">
            <? if (isset($item['table_id'])): ?>
                [<?= $item['table_id']?>]
            <? endif; ?>

            <?= $item['object_name'] ?>
            <? if ($item['isset_table'] == 1 && $item['change'] == 0): ?>
                <span class="ok">ОК</span>
            <? endif ?>
            <? if ($item['isset_table'] == 1 && $item['change'] == 1): ?>
                <span class="error">Error</span>
            <? endif ?>
            <? if ($item['isset_table'] == 0): ?>
                <span class="none">Not exists</span>
            <? endif ?>
            <? if ($item['isset_table'] == 1 && $item['empty'] == 1): ?>
                <span class="empty">(empty)</span>
            <? endif ?>

        </div>
        <div class="b-model__info b-model__<?= $item['table_name'] ?>">
            <? if (sizeof($item['query'])): ?>
                <div class="b-model__sql">
                    <b>SQL insert:</b>
                    <? foreach ($item['query'] as $sql): ?>
                        <div><?= $sql ?></div>
                    <? endforeach ?>
                </div>
            <? endif; ?>
            <table class="table table-bordered table-hover table-striped table-condensed">
                <tr>
                    <th>ORM</th>
                    <th>SQL</th>
                </tr>
                <? foreach ($item['table_columns'] as $table): ?>
                    <tr>
                        <th class="text-center" colspan="2">
                            <?= $table['column_name'] ?>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <? foreach ($table as $name => $value): ?>
                                <div class="value">
                                    [<?= $name ?>]=>"<?= $value ?>"
                                </div>
                            <? endforeach; ?>
                        </td>
                        <td>
                            <? if (sizeof($item['list_columns'][$table['column_name']])): ?>
                                <? foreach ($item['list_columns'][$table['column_name']] as $name => $value): ?>
                                    <div class="value">
                                        [<?= $name ?>]=>"<?= $value ?>"
                                    </div>
                                <? endforeach; ?>
                            <? endif; ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            </table>

        </div>

    </div>
<? endforeach; ?>
<style type="text/css">
    .b-model {
        border: 1px solid #ccc;
        padding: 3px;
    }

    .b-model__class {
        text-transform: uppercase;
        cursor: pointer;
    }

    .b-model__class:hover {
        background-color: #f5f5f5;
    }

    .b-model__class .ok {
        color: green;
        font-weight: bold;
    }

    .b-model__class .none {
        color: #ff0000;
    }

    .b-model__class .error {
        color: #ff0000;
    }

    .b-model__class .empty {
        color: #ccc;
        text-transform: lowercase;
    }

    .b-model__info {
        display: none;
    }

    .b-model__info.hover {
        display: block;
    }

    .b-model__sql {
        background-color: #c8e5bc;
        padding: 5px;

    }
</style>

<script type="text/javascript">
    function name_togget(select) {
        if ($(select).hasClass('hover')) {
            $(select).removeClass('hover');
        } else {
            $('.b-model__info').removeClass('hover');
            $(select).addClass('hover');
        }

    }
</script>

