<div ng-controller="VendorEditController" ng-init="init()">

    <div class="clearfix btn-group ">
        <a title="Назад" class="btn btn-danger btn-xs" href="<?= link_to('admin_vendor', array('action' => 'list')) ?>">
            <i class=" glyphicon glyphicon-arrow-left"></i>
        </a>
    </div>

    <h1>Производитель</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#vendor" data-toggle="tab">Описание</a></li>
        <li><a href="#meta" data-toggle="tab">SEO</a></li>
    </ul>


    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="vendor">

                <div ng-show="vendor.vendor_id">

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="image">Загрузка</label>

                        <div class="col-sm-10">
                            <input type="file" ng-file-select="onFileSelect($files,vendor.vendor_id)">
                        </div>
                    </div>

                    <div class="form-group" ng-if="image.image_id">
                        <label class="col-sm-2 control-label" for="image">Фото</label>

                        <div class="col-sm-10">
                            <img ng-src="{{image.preview}}" alt="" width="100"/>

                            <a href="<?= link_to('admin_image',array('action'=>'edit'))?>?id={{image.image_id}}"
                               title="Редактировать" class="btn btn-info btn-xs" >
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="vendor.name" class="form-control"
                               placeholder="Название"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">ЧПУ</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="vendor.url" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text">Описание</label>

                    <div class="col-sm-10">
                        <textarea id="text" ng-model="vendor.text" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Позиция</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="vendor.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Необязательное поле</span>
                    </div>
                </div>
            </div>
            <!-- #vendor -->

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
                            <input id="og_title" ng-model="meta.options.og.title" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="og_description">og:description</label>

                        <div class="col-sm-10">
                            <textarea id="og_description" ng-model="meta.options.og.description" class="form-control" onblur=""></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="og_image">og:image</label>

                        <div class="col-sm-10">
                            <input id="og_image" ng-model="meta.options.og.image" class="form-control" />
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
    window._vendor = <?= $vendor? \Delorius\Utils\Json::encode((array)$vendor): '{}'?>;
    window._meta = <?= $meta? \Delorius\Utils\Json::encode((array)$meta): '{}'?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): 'null'?>;
</script>





