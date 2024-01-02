<div ng-controller="ImagesListCtrl" ng-init='init()'>

    <form class="form-inline well well-lg">
        <div class="form-group">
            <label for="id">ID image: </label>
            <input ng-model="get.image_id" type="text" class="form-control" id="id" placeholder="image_id"
                   style="width:100px !important;margin-right: 10px;">
        </div>
        <div class="form-group">
            <label for="id">ID источника: </label>
            <input ng-model="get.id" type="text" class="form-control" id="id" placeholder="{ID}"
                   style="width:100px !important;margin-right: 10px;">
        </div>
        <div class="form-group" style="margin-right: 20px;">
            <label for="table_id">Категория: </label>
            <select ui-select2 name="table_id" ng-model="get.table_id" style="width: 200px;">
                <option value="">Все изображения</option>
                <option value="{{t.target_type}}"
                        ng-repeat="t in types">{{t.target_name}} ({{t.count}})
                </option>
            </select>
        </div>
        <button ng-click="search()" type="button" class="btn btn-success">Искать</button>
        <button ng-click="cancel()" type="button" class="btn btn-default">Отмена</button>
    </form>


    <div>
        <div>Кол-во изображений: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover table-middle">
        <tr>
            <th>Фото</th>
            <th width="100">TBL(ID)</th>
            <th>Инфо</th>
            <th>Название</th>
            <th width="50" class="i-center-td">Поз.</th>
            <th>#</th>
        </tr>
        <tr ng-repeat="image in images">
            <td width="60" align="center" class="gallery">
                <a href="{{image.normal}}" title="Normal image size: {{image.width}}x{{image.height}}">
                    <img width="50" ng-src="{{image.preview}}" alt=""/>
                </a>
                <a href="{{image.preview}}" title="Preview image size: {{image.pre_width}}x{{image.pre_height}}"></a>
            </td>
            <td>
                [image:{{image.image_id}}]<br/>
                [image:{{image.image_id}}?thumb]<br/>

                <b>{{image.target_type}}({{image.target_id}})</b>
            </td>
            <td>
                формат:
                <i ng-if="image.horizontal == '1'" title="Горизонтальная"
                   class="glyphicon glyphicon-option-horizontal"></i>
                <i ng-if="image.horizontal == '0'" title="Вертикальная" class="glyphicon glyphicon-option-vertical"></i>
                <br/>
                width: {{image.width}}px<br/>
                height: {{image.height}}px
            </td>
            <td valign="middle">
               {{image.name}}
            </td>
            <td class="i-center-td">
                <input ng-model="image.pos" style="width: 50px; text-align: center;" ng-blur="change_pos(image)"/>
            </td>
            <td width="105" class="i-center-td">
                <div class="btn-group">
                    <div class="btn-group">
                        <a href="javascript:;" class="btn btn-danger btn-xs" title="Удалить"
                           ng-click="delete(image)">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
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
                        <label for="upload_{{image.id}}" href="javascript:;" class="btn btn-success btn-xs">
                            <i class="glyphicon glyphicon-download-alt"></i>
                            <input id="upload_{{image.id}}" type="file" ng-file-select="onFileSelect($files,image.id)">
                        </label>


                    </div>
                </div>
            </td>
        </tr>
    </table>

    <?= $pagination->render(); ?>
</div>

<script type="text/javascript">
    window._images = <?= $images? \Delorius\Utils\Json::encode((array)$images): '[]' ?>;
    window._types = <?= $types? \Delorius\Utils\Json::encode((array)$types): '[]' ?>;
    window._get = <?= $get? \Delorius\Utils\Json::encode((array)$get): '{}' ?>;
</script>


