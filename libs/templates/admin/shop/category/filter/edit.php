<div ng-controller="CategoryFilterController" ng-init="init(<?= $cid ?>)">

    <div class="clearfix btn-group ">
        <a title="Назад" href="<?= link_to('admin_category_filter', array('action' => 'list', 'cid' => $cid)); ?>"
           class="btn btn-danger btn-xs"><i class=" glyphicon glyphicon-arrow-left"></i></a>
        <a class="btn btn-info btn-xs"
           href="<?= link_to('admin_category_filter', array('action' => 'add', 'cid' => $cid)) ?>"
           title="Добавить статический фильтр">
            <i class="glyphicon glyphicon-plus"></i>
        </a>

    </div>
    <br/>

    <h1>Фильтр</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#desc" data-toggle="tab">Описание</a></li>
        <li><a href="#meta" data-toggle="tab">SEO параметры</a></li>
    </ul>

    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="desc">

                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="filter.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="hash">HASH</label>

                    <div class="col-sm-10">
                        <input type="text" id="hash" ng-model="filter.hash" class="form-control"/>

                        <p class="help-block">hash - набор фильтров</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="header">Короткое название</label>

                    <div class="col-sm-10">
                        <input type="text" id="header" ng-model="filter.name" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="header">Заголовок</label>

                    <div class="col-sm-10">
                        <input type="text" id="header" ng-model="filter.header" class="form-control"
                               placeholder="Заголовок фильтра"/>

                        <p class="help-block">Может использоваться в h1 для текущей фильтре</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="filter.url" class="form-control"/>
                        <span class="help-block">Генерируется автоматически</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text_top">Текст сверху</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text_top" ng-model="filter.text_top"
                                  class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text_below">Текст снизу</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text_below" ng-model="filter.text_below"
                                  class="form-control"></textarea>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс шаблона списка товаров</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="filter.prefix" class="form-control"
                               placeholder="shop/goods/list_*"/>

                        <p class="help-block">Для выбора не стандартного отображения списка товаров
                            shop/goods/list_{prefix}</p>
                    </div>
                </div>

            </div>
            <!-- #desc -->

            <div class="tab-pane" id="meta">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">Заголовок страницы</label>

                    <div class="col-sm-10">
                        <input type="text" id="title" ng-model="meta.title" class="form-control"
                               placeholder="Заголовок страницы"/>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="keys">Ключевые слова</label>

                    <div class="col-sm-10">
                        <textarea id="keys" ng-model="meta.keys" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="desc">Краткое описания страницы</label>

                    <div class="col-sm-10">
                        <textarea id="desc" ng-model="meta.desc" class="form-control" onblur=""></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="redirect">Редирект</label>

                    <div class="col-sm-10">
                        <input type="text" id="redirect" ng-model="meta.redirect" class="form-control"
                               placeholder="Адрес ссылки"/>
                        <span class="help-block">Если необходиво перенаправить пользователя при переходе</span>
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
    window._filter = <?= $filter ? \Delorius\Utils\Json::encode((array)$filter) : '{status:"1",cid:"' . $cid . '"}'?>;
    window._meta = <?= $meta ? \Delorius\Utils\Json::encode((array)$meta) : '{}'?>;
</script>




