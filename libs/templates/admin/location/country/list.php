<div ng-controller="CountriesListCtrl" ng-init="init()">

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th class="i-center-td" width="55">Фото</th>
            <th>Название</th>
            <th>ЧПУ</th>
            <th width="50" >pos</th>
            <th width="20"></th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td><input name="name" ng-model="form.name" class="form-control" /></td>
            <td><input name="url" ng-model="form.url" class="form-control" /></td>
            <td><input name="pos" ng-model="form.pos" class="form-control " /></td>
            <td>
                <a title="Добавить" class="btn btn-xs btn-success" ng-click="add()" href="javascript:;" >
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </td>
        </tr>
        <tr ng-repeat="item in countries">
            <td class="i-center-td" >{{item.id}}</td>
            <td>
                <label class="b-input-upload" for="img_{{item.id}}">
                    <img width="50" ng-src="{{getImageSrc(item.id)}}" alt=""/>
                    <input id="img_{{item.id}}" type="file" ng-file-select="onFileSelect($files,item.id)" title="Загрузить фото" />
                </label>
            </td>
            <td class="i-center-td"><input name="name" ng-model="item.name" class="form-control" ng-blur="edit(item)" /></td>
            <td class="i-center-td"><input name="url" ng-model="item.url" class="form-control" ng-blur="edit(item)" /></td>
            <td class="i-center-td"><input name="pos" ng-model="item.pos" class="form-control text-center " ng-blur="edit(item)"  /></td>
            <td class="i-center-td">

                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a tabindex="-1" href="<?= link_to('admin_city', array('action' => 'list')) ?>?country_id={{item.id}}">
                                <i class="glyphicon glyphicon-home"></i> Города
                            </a>
                        </li>

                        <li ng-if="getImageId(item.id) !=0">
                            <a href="<?= link_to('admin_image', array('action' => 'edit')) ?>?id={{getImageId(item.id)}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать картинку</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="delete(item.id);">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

</div>
<script type="text/javascript">
    window._countries = <?= $countries? \Delorius\Utils\Json::encode((array)$countries): '[]' ?>;
    window._images = <?= $images ? \Delorius\Utils\Json::encode($images): '[]' ?>;
</script>