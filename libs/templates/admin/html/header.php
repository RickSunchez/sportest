<div class="container-fluid">
    <div class="row">
        <div class="col-md-offset-3 col-lg-offset-2 col-md-9 col-lg-8">
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

                    <a class="navbar-brand" href="<?= link_to('admin'); ?>" class="brand">Панель управления</a>
                </div>


                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <!-- Collect the nav links, forms, and other content for toggling-->
                    <ul class="nav navbar-nav">

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Пользователи <b
                                        class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= link_to('admin_root', array('action' => 'list')) ?>">Администраторы</a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="<?= link_to('admin_user', array('action' => 'list')) ?>">Пользователи</a>
                                </li>
                                <li><a href="<?= link_to('admin_attr', array('action' => 'list')) ?>">Атрибуты
                                        пользователей </a></li>
                                <li><a href="<?= link_to('admin_acl', array('action' => 'roles', 'type' => 'user')) ?>">Права
                                        доступа</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Настройки <b
                                        class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= link_to('admin_robots', array('action' => 'list')) ?>">Файл:
                                        robots.txt</a></li>
                                <li><a href="<?= link_to('admin_analytics', array('action' => 'list')) ?>">Код для
                                        аналитики</a></li>
                                <li><a href="<?= link_to('admin_fileIndex', array('action' => 'list')) ?>">Индексовые
                                        файлы</a></li>
                                <li><a href="<?= link_to('admin_migration', array('action' => 'index')) ?>">Миграция
                                        (SQL)</a></li>
                                <li class="divider"></li>
                                <li class="dropdown-header active" style="color: #333333"><b>Кэш</b> <b
                                            class="caret"></b></li>
                                <li><a href="javascript:;" ng-click="clearCache()">Очистить кэш файлов</a></li>
                                <li><a href="javascript:;" ng-click="clearCacheThumb()">Очистить кеш картинок</a></li>
                                <li><a href="javascript:;" ng-click="clearCacheTheme()">Очистить кеш css|js</a></li>

                                <li class="divider"></li>
                                <li class="dropdown-header active" style="color: #333333"><b>Sitemaps.xml</b> <b
                                            class="caret"></b></li>
                                <li><a href="javascript:;" ng-click="createSitemaps()">Сгенерировать</a></li>
                                <li><a href="javascript:;" ng-click="clearSitemaps()">Удалить</a></li>
                            </ul>
                        </li>
                        <!--                <li class="dropdown">-->
                        <!--                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Маркетинг <b class="caret"></b></a>-->
                        <!--                    <ul class="dropdown-menu">-->
                        <!--                        <li><a href="-->
                        <? //= link_to('admin_go',array('action'=>'list'))?><!--">Короткие ссылки</a></li>-->
                        <!--                        <li class="dropdown-header active" style="color: #333333" ><b>E-mail рассылка</b></li>-->
                        <!--                        <li><a href="-->
                        <? //= link_to('admin_delivery',array('action'=>'mail'))?><!--">Отправить письмо</a></li>-->
                        <!--                        <li><a href="-->
                        <? //= link_to('admin_subscriber',array('action'=>'list'))?><!--">Подписчики</a></li>-->
                        <!--                        <li><a href="-->
                        <? //= link_to('admin_subscription',array('action'=>'list'))?><!--">Группа подписок</a></li>-->
                        <!--                        <li><a href="-->
                        <? //= link_to('admin_delivery',array('action'=>'list'))?><!--">Рассылка писем</a></li>-->
                        <!--                    </ul>-->
                        <!--                </li>-->
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?= $this->action('CMS:Admin:Html:helpDesk'); ?>
                        <?= $this->action('CMS:Admin:Html:callback'); ?>
                        <li><a href="<?= link_to('homepage') ?>">Перейти на сайт</a></li>
                        <li><a href="<?= link_to('admin_logout') ?>">Выход</a></li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </nav>
        </div>
    </div>
</div>
