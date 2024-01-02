<div class="container">
    <nav class="navbar navbar-default" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Навигация</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand"  href="<?= link_to('cabinet'); ?>" class="brand">Личный кабинет</a>
        </div>


        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav navbar-right">
                <?= $this->action('CMS:Cabinet:Html:helpDesk');?>
                <?= $this->action('CMS:Cabinet:Html:balance');?>
                <li>
                    <a href="/">
                        <i class="glyphicon glyphicon-new-window"></i> Перейти на сайт
                    </a>
                </li>
                <li>
                    <a href="<?= link_to('user_login',array('action'=>'logout')) ?>">
                        <i class="glyphicon glyphicon-log-out" ></i> Выход
                    </a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>

</div>
