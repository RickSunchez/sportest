<div ng-controller="EventController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_event', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Событие</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#desc" data-toggle="tab">Описание</a></li>
        <li><a href="#meta" data-toggle="tab">SEO параметры</a></li>
    </ul>

    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="desc">

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" ng-model="event.status" ng-true-value="'1'" id="inputstatus"
                                       ng-false-value="'0'"/> Опубликовать
                            </label>
                        </div>
                    </div>
                </div>

            <span ng-show="event.id">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="image">Загрузка</label>

                    <div class="col-sm-10">
                        <input type="file" ng-file-select="onFileSelect($files,event.id)">
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

                <? if ($multi): ?>
                    <div class="form-group">
                        <label for="inputsite" class="col-sm-2 control-label">Домен</label>

                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <select
                                    ui-select2
                                    name="site"
                                    ng-model="event.site" style="width: 100%;">
                                    <option value="{{d.name}}"
                                            ng-repeat="d in domain">{{d.host}}
                                    </option>
                                </select>
                            </p>
                        </div>
                    </div>
                <? endif; ?>

                <div class="form-group" ng-show="event.type_id != 3" ng-if="show_cat()">
                    <label for="inputcid" class="col-sm-2 control-label">Раздел</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <select ui-select2 name="cid" ng-model="event.cid" style="width: 100%;">
                                <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                        ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                                </option>
                            </select>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="event.name" class="form-control"
                               placeholder="Название новости"/>
                    </div>
                </div>

                <div class="form-group" ng-show="event.type_id != 3">
                    <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="event.url" class="form-control"/>
                        <span class="help-block">Генерируется автоматически</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="location">Адрес:</label>

                    <div class="col-sm-10">
                        <input type="text" id="location" ng-model="event.location" class="form-control"
                               placeholder="Место проведения"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="location_name">Название места:</label>

                    <div class="col-sm-10">
                        <input type="text" id="location_name" ng-model="event.location_name" class="form-control"
                               placeholder="Названия места проведения"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="date_alarm">Дата начала</label>

                    <div class="col-sm-3">
                        <input type="text" id="date_alarm" ng-model="event.date_cr" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="date_alarm">Дата окончания</label>

                    <div class="col-sm-3">
                        <input type="text" id="date_end" ng-model="event.date_end" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="preview">Анонс</label>

                    <div class="col-sm-10">
                        <textarea style="height:100px; " name="preview" id="preview" ng-model="event.preview"
                                  class="form-control"
                                  placeholder="Краткое описание события"></textarea>
                    </div>
                </div>

                <div class="form-group" ng-show="event.type_id != 3">
                    <label class="col-sm-2 control-label" for="text">Описание</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text" ng-model="event.text" class="form-control"
                                  placeholder="Описание новости"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс страницы</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="event.prefix" class="form-control" placeholder="cms/event/show_*"/>

                        <p class="help-block">Для выбора не стандартного отображения страницы
                            cms/event/show_{prefix}</p>
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
    window._event = <?= $event? \Delorius\Utils\Json::encode((array)$event): '{site:"www",cid:0,status:"0",main:0}'?>;
    window._meta = <?= $meta? \Delorius\Utils\Json::encode((array)$meta): '{}'?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): 'null'?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson',array('pid'=>0,'typeId'=>\CMS\Catalog\Entity\Category::TYPE_EVENT,'placeholder'=>'Без категории'));?>;
    window._domain = <?= $domain ? \Delorius\Utils\Json::encode((array)$domain): '{}'?>;

    $('#date_alarm,#date_end').datetimepicker({
        lang: 'ru',
        timepicker: true,
        format: 'd.m.Y H:i'
    });
</script>




