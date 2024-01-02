<div ng-controller="ArticleController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_article', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Статья</h1>

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
                            <input type="checkbox" ng-model="article.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать статью</p>
                    </div>
                </div>

                <? if ($multi): ?>
                    <div class="form-group">
                        <label for="inputsite" class="col-sm-2 control-label">Домен</label>

                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <select
                                    ui-select2
                                    name="site"
                                    ng-model="article.site" style="width: 100%;">
                                    <option value="{{d.name}}"
                                            ng-repeat="d in domain">{{d.host}}
                                    </option>
                                </select>
                            </p>
                        </div>
                    </div>
                <? endif; ?>

                <div class="form-group" ng-if="show_cat()">
                    <label for="inputcid" class="col-sm-2 control-label">Раздел</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <select ui-select2 name="cid" ng-model="article.cid" style="width: 100%;">
                                <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                        ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                                </option>
                            </select>
                        </p>
                    </div>
                </div>


            <span ng-show="article.id">
            <div class="form-group">
                <label class="col-sm-2 control-label" for="image">Загрузка</label>

                <div class="col-sm-10">
                    <input type="file" ng-file-select="onFileSelect($files,article.id)">
                </div>
            </div>

            <div class="form-group" ng-if="image.image_id">
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
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="article.name" class="form-control"
                               placeholder="Название статьи"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="date_alarm">Дата создания</label>

                    <div class="col-sm-3">
                        <input type="text" id="date_alarm" ng-model="article.date_cr" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="article.url" class="form-control"/>
                        <span class="help-block">Генерируется автоматически</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="preview">Анонс</label>

                    <div class="col-sm-10">
                        <textarea name="preview" id="preview" ng-model="article.preview" class="form-control"
                                  placeholder="Краткое описание статьи"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text">Описание</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text" ng-model="article.text" class="form-control"
                                  placeholder="Описание новости"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="source">Источник</label>

                    <div class="col-sm-10">
                        <input type="text" id="source" ng-model="article.source" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputtags" class="col-sm-2 control-label">Tags</label>

                    <div class="col-sm-10">
                        <tag-input taglist='tags' autocomplete='true'
                                   source='<?= link_to('admin_article_data', array('action' => 'tags')) ?>'></tag-input>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Кол-во просмотров</label>

                    <div class="col-sm-2">
                        <input type="text" ng-model="article.views" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс страницы</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="article.prefix" class="form-control"
                               placeholder="cms/article/show_*"/>

                        <p class="help-block">Для выбора не стандартного отображения страницы
                            cms/article/show_{prefix}</p>
                    </div>
                </div>
            </div>
            <!-- #deac -->

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
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._article = <?= $article? \Delorius\Utils\Json::encode((array)$article): '{site:"www",status:"0",cid:'.($cid?$cid:'0').'}'?>;
    window._meta = <?= $meta? \Delorius\Utils\Json::encode((array)$meta): '{}'?>;
    window._tags = <?= $tags? \Delorius\Utils\Json::encode((array)$tags): '[]'?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): 'null'?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson',array('pid'=>0,'typeId'=>\CMS\Catalog\Entity\Category::TYPE_ARTICLE,'placeholder'=>'Без категории'));?>;
    window._domain = <?= $domain ? \Delorius\Utils\Json::encode((array)$domain): '{}'?>;


    $('#date_alarm').datetimepicker({
        lang: 'ru',
        timepicker: true,
        format: 'd.m.Y H:i'
    });
</script>




