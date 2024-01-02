<h2>ORM</h2>
<div style="margin: 10px 0">
    <a class="btn btn-success btn-xs" href="<?= link_to('admin_migration', array('action' => 'index')) ?>">
        Назад
    </a>
</div>
<? foreach ($items as $key => $item): ?>
    <div style="padding: 5px;border: 1px solid #000;">
        <?= $key + 1 ?>) <?= $item ?>
    </div>
<? endforeach; ?>
