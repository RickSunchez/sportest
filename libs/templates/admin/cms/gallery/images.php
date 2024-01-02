<div ng-controller="GalleryImagesListController" ng-init="init()">
    <div class="clearfix btn-group ">
        <a title="Назад" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_gallery', array('action' => 'list')) ?>">
            <i class=" glyphicon glyphicon-arrow-left"></i>
        </a>
    </div>
    <h1>Галерея</h1>
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#desc" data-toggle="tab">Описание</a></li>
        <li ng-show="gallery.gallery_id"><a href="#images" data-toggle="tab">Изображения</a></li>
    </ul>
    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="desc">

                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="gallery.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать</p>
                    </div>
                </div>

                <? if ($multi): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">Выберите сайт:</label>

                        <div class="col-sm-10">
                            <select id="domains" ui-select2 ng-model="select_domain" ng-change="select()">
                                <option value="{{d.name}}" ng-repeat="d in domain">{{d.host}}</option>
                            </select>
                        </div>
                    </div>
                <? endif ?>

                <div class="form-group" ng-if="show_cat()" >
                    <label class="col-sm-2 control-label" for="name">Категория</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="gallery.cid" style="width: 100%">
                            <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                    ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="gallery.name" class="form-control"
                               placeholder="Название товара"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="brief">Описание</label>

                    <div class="col-sm-10">
                        <textarea id="brief" ng-model="gallery.note" class="form-control"></textarea>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Приоритет</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="gallery.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Необязательное поле</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
                    </div>
                </div>

            </div>
            <!-- #desc -->

            <div class="tab-pane" id="images">
                <div class="clearfix">
                    Добавить фото: <input type="file" ng-file-select="onFileSelect($files,gallery.gallery_id)"
                                          multiple/>
                </div>
                <br clear="all"/><br clear="all"/>
                <table class="table table-condensed table-bordered table-hover">
                    <tr>
                        <th>ID</th>
                        <th>Фото</th>
                        <th>Название</th>
                        <th width="80">Приоритет</th>
                        <th>#</th>
                    </tr>
                    <tr ng-repeat="image in images">
                        <td width="20" align="center">
                            {{image.image_id}}
                        </td>
                        <td width="60" align="center">
                            <a href="{{image.normal}}" class="image-link">
                                <img width="50" ng-src="{{image.preview}}" alt="{{gallery.name}}"/>
                            </a>
                        </td>
                        <td valign="middle">
                            <div class="input-group">
                                <input name="name" ng-model="image.name" class="form-control"/>
                                <span title="Сохранить название" class="input-group-addon btn-success"
                                      ng-click="saveImage(image)"
                                      style="cursor: pointer;color: #ffffff;">
                                <i class="glyphicon glyphicon-ok"></i>
                                </span>
                            </div>
                        </td>
                        <td class="i-center-td">
                            <input ng-model="image.pos" class="pos" ng-change="change(image)"/>
                        </td>
                        <td width="90" class="i-center-td">
                            <div class="btn-group">
                                <a href="javascript:;" class="btn btn-danger btn-xs"
                                   ng-click="delete(image)"><i class="glyphicon glyphicon-trash"></i></a>

                                <a ng-if="image.main == '0'" href="javascript:;" class="btn btn-default btn-xs"
                                   ng-click="main(image,1)">
                                    <i class="glyphicon glyphicon-star"></i>
                                </a>
                                <a ng-if="image.main == '1'" href="javascript:;" class="btn btn-warning btn-xs"
                                   ng-click="main(image,0)">
                                    <i class="glyphicon glyphicon-star"></i>
                                </a>
                                <a href="<?= link_to('admin_image', array('action' => 'edit')) ?>?id={{image.image_id}}"
                                   title="Редактировать" class="btn btn-info btn-xs">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    window._images = <?= $images ? \Delorius\Utils\Json::encode($images) : '[]' ?>;
    window._gallery = <?= $gallery ? \Delorius\Utils\Json::encode($gallery) : '{site:www}' ?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson',array('pid'=>0,'typeId'=>\CMS\Catalog\Entity\Category::TYPE_GALLERY,'placeholder'=>'Без категории'));?>;
    window._domain = <?= $domain ? \Delorius\Utils\Json::encode((array)$domain): '{}'?>;
    window._select_domain = '<?= $get['domain']?$get['domain']:'www'?>';
</script>