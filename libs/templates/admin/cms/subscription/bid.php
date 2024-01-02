<table class="table table-striped table-bordered table-hover">
    <tr>
        <td>Имя</td>
        <td>Телефон</td>
        <td><a href="<?= isset($get['sort'])? '?sort=date': '?' ?>">Дата</a></td>
    </tr>
<?foreach($bid as $item):?>
    <tr>
        <td><?= $item->name?></td>
        <td><?= $item->phone?></td>
        <td><?= date('H:i d-M-Y',$item->date_cr)?></td>
    </tr>
<?endforeach;?>
</table>

<?= $pagination->render(); ?>