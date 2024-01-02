<div ng-controller="CategoryCMSIdsController" ng-init="init()">
    <div class="clearfix btn-group ">
        <a title="Назад" href="<?= link_to('admin_cms_category', array('action' => 'list', 'type_id' => $type_id)); ?>"
           class="btn btn-danger btn-xs"><i class=" glyphicon glyphicon-arrow-left"></i></a>

    </div>
    <br/>

    <h1>Категории</h1>

    <div>ID: {{ids|json}}</div>
    <br/>
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
                            <input type="checkbox" ng-model="category.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать категорию</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Вложенность</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="category.pid" style="width: 100%">
                            <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                    ng-repeat="cat in select">{{cat.seporator}} {{cat.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="description">Текст</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="description" ng-model="category.description"
                                  class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Приоритет</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="category.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Не обязательное поле</span>
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

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_title">og:title</label>

                    <div class="col-sm-10">
                        <input id="og_title" ng-model="meta.options.og.title" class="form-control"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_description">og:description</label>

                    <div class="col-sm-10">
                        <textarea id="og_description" ng-model="meta.options.og.description" class="form-control"
                                  onblur=""></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_image">og:image</label>

                    <div class="col-sm-10">
                        <input id="og_image" ng-model="meta.options.og.image" class="form-control"/>
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
    window._ids = '<?= $ids?>';
    window._select = <?= $select ? \Delorius\Utils\Json::encode((array)$select): '[]'?>;
    window._type_id = <?= $type_id?>;
</script>




