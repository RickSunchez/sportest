<aside class="b-login__layout">
    <a class="b-login__link" href="<?= link_to('cabinet'); ?>">
        <i class="glyphicon glyphicon-user"></i> Личный кабинет
    </a>
    <a class="b-login__link" title="Выход" href="<?= link_to('user_login', array('action' => 'logout')); ?>">
        <i class="glyphicon glyphicon-log-out"></i>
    </a>
</aside>