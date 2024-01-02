<div ng-controller="VideoController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_video', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Видео</h1>

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
                                <input type="checkbox" ng-model="video.status" ng-true-value="'1'" id="inputstatus"
                                       ng-false-value="'0'"/> Опубликовать
                            </label>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="image">Загрузка</label>

                    <div class="col-sm-10">
                        <input type="file" ng-file-select="onFileSelect($files,video.id)">
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


                <div class="form-group" ng-if="show_cat()">
                    <label for="inputcid" class="col-sm-2 control-label">Раздел</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <select ui-select2 name="cid" ng-model="video.cid" style="width: 100%;">
                                <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                        ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                                </option>
                            </select>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL видео</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="video.url" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="video.name" class="form-control"
                               placeholder="Название новости"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text">Описание</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text" ng-model="video.text" class="form-control"
                                  placeholder="Описание видео"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputtags" class="col-sm-2 control-label">Tags</label>

                    <div class="col-sm-10">
                        <tag-input taglist='tags' autocomplete='true'
                                   source='<?= link_to('admin_video_data', array('action' => 'tags')) ?>'></tag-input>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс страницы</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="video.prefix" class="form-control" placeholder="cms/video/show_*"/>

                        <p class="help-block">Для выбора не стандартного отображения страницы
                            cms/video/show_{prefix}</p>
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
                        <textarea id="og_description" ng-model="meta.options.og.description" class="form-control"></textarea>
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
    window._video = <?= $video? \Delorius\Utils\Json::encode((array)$video): '{cid:0,status:"0",main:0}'?>;
    window._tags = <?= $tags? \Delorius\Utils\Json::encode((array)$tags): '[]'?>;
    window._meta = <?= $meta? \Delorius\Utils\Json::encode((array)$meta): '{}'?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): 'null'?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson',array('pid'=>0,'typeId'=>\CMS\Catalog\Entity\Category::TYPE_VIDEO,'placeholder'=>'Без категории'));?>;
</script>




