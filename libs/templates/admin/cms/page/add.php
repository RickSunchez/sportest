<div ng-controller="PageController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_page', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Страница</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#page" data-toggle="tab">Страница</a></li>
        <li><a href="#template" data-toggle="tab">Шаблон</a></li>
        <li><a href="#meta" data-toggle="tab">SEO параметры</a></li>
    </ul>

    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="page">


                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" ng-model="page.status" ng-true-value="'1'" id="inputstatus"
                                       ng-false-value="'0'"/> Опубликовать
                            </label>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">Заголовок страницы</label>

                    <div class="col-sm-10">
                        <input type="text" id="title" ng-model="page.title" class="form-control" placeholder="Title"/>
                    </div>
                </div>

            <span ng-show="page.id">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="image">Загрузка</label>

                    <div class="col-sm-10">
                        <input type="file" ng-file-select="onFileSelect($files, page.id)">
                    </div>
                </div>

                <div class="form-group" ng-show="image.image_id">
                    <label class="col-sm-2 control-label" for="image">Фото</label>

                    <div class="col-sm-10">
                        <img ng-src="{{image.preview}}" alt="" width="100"/>
                        <a href="<?= link_to('admin_image', array('action' => 'edit')) ?>?id={{image.image_id}}"
                           title="Редактировать" class="btn btn-info btn-xs">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </a>
                    </div>
                </div>
            </span>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="short_title">Короткое название</label>

                    <div class="col-sm-10">
                        <input type="text" id="short_title" ng-model="page.short_title" class="form-control"
                               placeholder="BreadCrumbs"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="short_title">Parent ID</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="page.pid" class="form-control"
                               placeholder="ID родителя"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="page.url" class="form-control"/>
                        <span class="help-block">Генерируется автоматически</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text">Текст</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text" ng-model="page.text" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Приоритет</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="page.pos" class="span1"/>
                        <span class="help-block">Не обязательное поле</span>
                    </div>
                </div>
            </div>
            <!-- #page -->

            <div class="tab-pane" id="template">
                <div class="form-group" ng-show="select_domain_form">
                    <label class="col-sm-2 control-label">Выберите сайт</label>
                    {{select_domain}}
                    <div class="col-sm-10">
                        <select ng-model="select_domain" ng-options="d.name as d.host for d in domains"
                                class="form-control">
                        </select>
                    </div>
                </div>

                <div class="form-group" ng-hide="select_domain_form">
                    <label class="col-sm-2 control-label">Сайт: </label>

                    <div class="col-sm-10">
                        <p class="form-control-static">{{ showHost() }}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Категория шаблона</label>

                    <div class="col-sm-10">
                        <select ng-model="dir" ng-options="dir.name as  dir.name for dir in templates.dir"
                                class="form-control">
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Шаблон страницы</label>

                    <div class="col-sm-10">
                        <select ng-model="tpl"
                                ng-options="template.name as template.name for template in templates.page[dir]"
                                class="form-control"
                                ng-change="up()">
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Мобильная версия</label>

                    <div class="col-sm-10">
                        <select ng-model="page.mobile" ng-options="dir.name as  dir.name for dir in templates.dir"
                                class="form-control">
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс страницы</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="page.prefix" class="form-control" placeholder="cms/page/show_*"/>

                        <p class="help-block">Для выбора не стандартного отображения страницы cms/page/show_{prefix}</p>
                    </div>
                </div>
            </div>
            <!-- #template -->
            <div class="tab-pane" id="meta">

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="key">Ключевые слова</label>

                    <div class="col-sm-10">
                        <textarea id="key" ng-model="page.keys" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="desc">Краткое описания страницы</label>

                    <div class="col-sm-10">
                        <textarea id="desc" ng-model="page.description" class="form-control" onblur=""></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="redirect">Редирект</label>

                    <div class="col-sm-10">
                        <input type="text" id="redirect" ng-model="page.redirect" class="form-control"
                               placeholder="Адрес ссылки"/>
                        <span class="help-block">Если необходиво перенаправить пользователя при переходе</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_title">og:title</label>

                    <div class="col-sm-10">
                        <input id="og_title" ng-model="options.og.title" class="form-control"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_description">og:description</label>

                    <div class="col-sm-10">
                        <textarea id="og_description" ng-model="options.og.description" class="form-control"
                                  onblur=""></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_image">og:image</label>

                    <div class="col-sm-10">
                        <input id="og_image" ng-model="options.og.image" class="form-control"/>
                    </div>
                </div>
            </div>
            <!-- #meta -->

        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">

    window._templates = <?= \Delorius\Utils\Json::encode($tpl)?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): 'null'?>;
    window._pid = <?= (int)$pid?>;
    window._page = <?= $page ? \Delorius\Utils\Json::encode((array)$page) : '{status:"1",title: "",short_title: "",text: "",pid: 0}'?>;
    window._dir = '<?= $default['template']?>';
    window._tpl = '<?= $default['layout']?>';
    window._mobile = '<?= $default['mobile']?>';
    window._domains = <?= $domains ? \Delorius\Utils\Json::encode($domains): '[]'?>;
    window._options = <?= $options ? \Delorius\Utils\Json::encode($options): '{}'?>;
    window._site = <?= $domain? '"'.$domain.'"': 'null'?>;

</script>




