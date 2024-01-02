<div ng-controller="NoteController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_schema', array('action' => 'edit', 'id' => $schema['id'])); ?>"
           class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>{{schema.name}}: {{note.name}}</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#desc" data-toggle="tab">Описание</a></li>
        <li><a href="#meta" data-toggle="tab">SEO параметры</a></li>
    </ul>

    <form class="form-horizontal well" role="form" id="note-components-form">
        <div class="tab-content">
            <div class="tab-pane active" id="desc">

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" ng-model="note.status" ng-true-value="'1'" id="inputstatus"
                                       ng-false-value="'0'"/> Опубликовать
                            </label>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="image">Загрузка</label>

                    <div class="col-sm-10">
                        <input type="file" ng-file-select="onFileSelect($files,note.id)">
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


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="note.name" class="form-control"
                               placeholder="Название узла"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">H1</label>

                    <div class="col-sm-10">
                        <input type="text" id="title" ng-model="note.title" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="note.url" class="form-control"/>
                    </div>
                </div>


                <h3>Элементы:</h3>

                <table class="table table-condensed table-bordered table-hover">
                    <!-- <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="1">
                            <a style="margin-right: -10px" title="Добавить товар"
                               class="btn btn-success btn-xs"
                               ng-click="load_product(item)"
                               href="javascript:;">
                                <i class="glyphicon glyphicon-plus"></i>
                            </a>
                        </td>
                    </tr> -->
                    <tr class="active">
                        <th width="20">ID</th>
                        <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
                        <th width="70">№</th>
                        <th width="150">Артикул</th>
                        <th>Название</th>
                        <th width="50" class="i-center-td"><i
                                    class="glyphicon glyphicon-sort-by-attributes-alt"></i>
                        </th>
                        <th width="20">
                            <a style="margin-right: -10px" title="Добавить товар"
                               class="btn btn-success btn-xs"
                               ng-click="load_product(item);"
                               href="javascript:;">
                                <i class="glyphicon glyphicon-plus"></i>
                            </a>
                        </th>
                    </tr>
                    <!-- <tr class="active">
                        <td></td>
                        <td></td>
                        <td><input name="number" ng-model="form.number" class="form-control"/></td>
                        <td><input name="article" ng-model="form.article" class="form-control"/></td>
                        <td><input name="name" ng-model="form.name" class="form-control"/></td>
                        <td><input name="pos" ng-model="form.pos" class="form-control "/></td>
                        <td class="i-center-td">
                            <a title="Добавить" class="btn btn-xs btn-success" ng-click="add()"
                               href="javascript:void(0);">
                                <i class="glyphicon glyphicon-plus"></i>
                            </a>
                        </td>
                    </tr> -->
                    <tr ng-repeat-start="item in items">
                        <td class="i-center-td">{{item.id}}</td>
                        <td class="i-center-td">
                                <span ng-if="item.status == 1"><i ng-click="status(item,0)"
                                                                  class="glyphicon glyphicon-eye-open"
                                                                  style="cursor: pointer;color: green;"></i></span>
                            <span ng-if="item.status == 0"><i ng-click="status(item,1)"
                                                              class="glyphicon glyphicon-eye-close"
                                                              style="cursor: pointer;"></i></span>
                        </td>
                        <td class="i-center-td">
                            <input name="number" ng-model="item.number" class="form-control text-center "
                                   ng-blur="edit(item)"/>
                        </td>
                        <td class="i-center-td">
                            <input name="article" ng-model="item.article" class="form-control text-center "
                                   ng-blur="edit(item)"/>
                        </td>
                        <td class="i-center-td">
                            <input name="name" ng-model="item.name" class="form-control  "
                                   ng-blur="edit(item)"/>
                        </td>
                        <td class="i-center-td">
                            <input name="pos" ng-model="item.pos" class="form-control text-center "
                                   ng-blur="edit(item)"/>
                        </td>
                        <td class="i-center-td">
                            <a title="Добавить" class="btn btn-xs btn-danger" ng-click="delete(item.id);"
                               href="javascript:void(0);">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <tr ng-repeat-end="item in items" class="warning">
                        <td colspan="2">Товар:</td>
                        <td colspan="4">
                            <div class="ellipsis" style="max-width: 750px;" ng-if="item.pid !=0">[{{item.pid}}]
                                {{get_product(item.pid)}}
                            </div>
                        </td>
                        <!-- #NK -->
                        <td colspan="1">
                            <a style="margin-right: -10px" title="Выбрать товар"
                               class="btn btn-success btn-xs"
                               ng-click="load_product(item)"
                               href="javascript:;">
                                <i class="glyphicon glyphicon-search"></i>
                            </a>
                        </td>
                    </tr>
                </table>


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
    window._note = <?= $note ? \Delorius\Utils\Json::encode((array)$note) : '{}'?>;
    window._schema = <?= $schema ? \Delorius\Utils\Json::encode((array)$schema) : '{}'?>;
    window._image = <?= $image ? \Delorius\Utils\Json::encode((array)$image) : 'null' ?>;
    window._meta = <?= $meta ? \Delorius\Utils\Json::encode((array)$meta) : '{}'?>;
</script>




