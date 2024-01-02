<div ng-controller="SchemaController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_schema', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Схема</h1>

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
                                <input type="checkbox" ng-model="schema.status" ng-true-value="'1'" id="inputstatus"
                                       ng-false-value="'0'"/> Опубликовать
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputpro" class="col-sm-2 control-label">Товар</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <a title="Выбрать товар"
                               class="btn btn-success btn-xs popup-link-ajax"
                               href="<?= link_to('admin_goods_data', array('action' => 'goodsList')) ?>?type_id=1">
                                <i class="glyphicon glyphicon-search"></i>
                            </a>
                            <span ng-show="product.goods_id">
                               [{{product.goods_id}}] {{product.name}}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputcid" class="col-sm-2 control-label">Каталог</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <select ui-select2 name="cid" ng-model="schema.cid" style="width: 100%;">
                                <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                        ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                                </option>
                            </select>
                        </p>
                    </div>
                </div>

                <div class="form-group" ng-if="vendors.length!=0">
                    <label class="col-sm-2 control-label" for="vendor">Производитель</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="schema.vid" style="width: 100%">
                            <option value="0">Производитель не указан</option>
                            <option title="{{v.name}}" ng-repeat="v in vendors" value="{{v.vendor_id}}">
                                {{v.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="schema.name" class="form-control"
                               placeholder="Название схемы"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">H1</label>

                    <div class="col-sm-10">
                        <input type="text" id="title" ng-model="schema.title" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="schema.url" class="form-control"/>
                    </div>
                </div>

                <div  ng-show="schema.id" class="form-group">
                    <label class="col-sm-2 control-label" for="image">Загрузка</label>

                    <div class="col-sm-10">
                        <input type="file" ng-file-select="onFileSelect($files,schema.id)">
                    </div>
                </div>

                <div class="form-group" ng-if="image.image_id">
                    <label class="col-sm-2 control-label" for="image">Фото</label>

                    <div class="col-sm-10">
                        <img ng-src="{{image.preview}}" alt="" width="100"/>
                        <!--                        <a href="-->
                        <? //= link_to('admin_image', array('action' => 'edit')) ?><!--?id={{image.image_id}}"-->
                        <!--                           title="Редактировать" class="btn btn-info btn-xs">-->
                        <!--                            <i class="glyphicon glyphicon-pencil"></i>-->
                        <!--                        </a>-->
                    </div>
                </div>


                <div ng-show="schema.id">
                    <h3>Узлы:</h3>

                    <table class="table table-condensed table-bordered table-hover">
                        <tr>
                            <th width="20">ID</th>
                            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
                            <th>Название</th>
                            <th width="60" class="i-center-td"><i
                                        class="glyphicon glyphicon-sort-by-attributes-alt"></i>
                            </th>
                            <th width="20"></th>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><input name="name" ng-model="form.name" class="form-control"/></td>
                            <td><input name="pos" ng-model="form.pos" class="form-control "/></td>
                            <td class="i-center-td">
                                <a title="Добавить" class="btn btn-xs btn-success" ng-click="add()"
                                   href="javascript:void(0);">
                                    <i class="glyphicon glyphicon-plus"></i>
                                </a>
                            </td>
                        </tr>
                        <tr ng-repeat="note in notes">
                            <td class="i-center-td">{{note.id}}</td>
                            <td class="i-center-td">
                                <span ng-if="note.status == 1"><i ng-click="status(note,0)"
                                                                  class="glyphicon glyphicon-eye-open"
                                                                  style="cursor: pointer;color: green;"></i></span>
                                <span ng-if="note.status == 0"><i ng-click="status(note,1)"
                                                                  class="glyphicon glyphicon-eye-close"
                                                                  style="cursor: pointer;"></i></span>
                            </td>
                            <td class="i-middle-td">
                                <a href="<?= link_to('admin_schema', array('action' => 'note')) ?>?id={{note.id}}">
                                    {{note.name}}
                                </a>
                            </td>
                            <td class="i-center-td">
                                <input name="pos" ng-model="note.pos" class="form-control text-center "
                                       ng-blur="edit(note)"/>
                            </td>


                            <td class="i-center-td">
                                <a title="Добавить" class="btn btn-xs btn-danger" ng-click="delete(note.id);"
                                   href="javascript:void(0);">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                            </td>


                        </tr>
                    </table>

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

<script>
    window._schema = <?= $schema ? \Delorius\Utils\Json::encode((array)$schema) : '{status:"0",cid:0}'?>;
    window._meta = <?= $meta ? \Delorius\Utils\Json::encode((array)$meta) : '{}'?>;
    window._product = <?= $product ? \Delorius\Utils\Json::encode((array)$product) : '{}'?>;
    window._vendors = <?= $vendors ? \Delorius\Utils\Json::encode((array)$vendors) : '[]'?>;
    window._image = <?= $image ? \Delorius\Utils\Json::encode((array)$image) : 'null' ?>;
</script>




