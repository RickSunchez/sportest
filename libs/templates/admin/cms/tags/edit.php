<div ng-controller="TagsCtrl" ng-init="init()">

    <div class="clearfix btn-group ">
        <a title="Назад" class="btn btn-danger btn-xs" href="<?= link_to('admin_tags', array('action' => 'list')) ?>">
            <i class=" glyphicon glyphicon-arrow-left"></i>
        </a>
    </div>

    <h1></h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#tags" data-toggle="tab">Описание</a></li>
        <li><a href="#meta" data-toggle="tab">SEO</a></li>
    </ul>


    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="tags">

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Tag</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="tag.name" class="form-control"
                               placeholder="Название"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="show">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="show" ng-model="tag.show" class="form-control"/>
                        <span class="help-block">Визуальное отображение на сайте</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text">Краткое описание</label>

                    <div class="col-sm-10">
                        <textarea id="text" ng-model="tag.text" class="form-control"></textarea>
                    </div>
                </div>

            </div>
            <!-- #tags -->

            <div class="tab-pane" id="meta">
                <fieldset>

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

                </fieldset>
            </div>
            <!-- #meta -->

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>

</div>

<script type="text/javascript">
    window._tag = <?= $tag ? \Delorius\Utils\Json::encode((array)$tag) : '{}'?>;
    window._meta = <?= $meta ? \Delorius\Utils\Json::encode((array)$meta) : '{}'?>;
</script>





