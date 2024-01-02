<div ng-controller="DocumentListController" ng-init="init()">

    <div class="clearfix">
        Добавить документ: <input type="file" ng-file-select="onFileSelect($files)" multiple />
    </div>
    <br clear="all" /><br clear="all" />

    <div>
        <div>Кол-во файлов: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="50">Тип</th>
            <th width="80">Размер</th>
            <th width="80">Файл</th>
            <th width="20">Кол-во</th>
            <th>Название</th>
            <th ng-if="show_cat()" width="150">Категория</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="file in files">
            <td>{{file.file_id}}</td>
            <td>{{file.ext}}</td>
            <td>{{file.file_size}}</td>
            <td>{{file.file_name}}</td>
            <td align="center">{{file.count}}</td>
            <td> {{file.title}}</td>
            <td ng-if="show_cat()" >
                <select ui-select2 name="cid" ng-model="file.cid" ng-change="changeCid(file)" style="width: 100%">
                    <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"  ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}</option>
                </select>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a
                                href="<?= link_to('admin_doc', array('action' => 'edit')) ?>?id={{file.file_id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a onclick="return confirm('Вы действительно хотите удалить файл?');"
                               href="javascript:void(0)" ng-click="delete(file)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить</a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
    <?= $pagination->render(); ?>
</div>

<script type="application/javascript">
    window._files = <?= $files? \Delorius\Utils\Json::encode($files): '[]' ?>;
    window._get = <?= $get? \Delorius\Utils\Json::encode($get): '[]' ?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson',array('pid'=>0,'typeId'=>\CMS\Catalog\Entity\Category::TYPE_DOCS,'placeholder'=>'Без категории'));?>;
</script>