<div ng-controller="CollectionListCtrl" ng-init='init()'>


    <div class="clearfix btn-group ">
        <a title="Добвить коллекцию" class="btn btn-success btn-xs"
           href="<?= link_to('admin_collection', array('action' => 'add')) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
        <button title="Поиск" ng-click="form_search = 1" type="button" class="btn btn-info btn-xs"><i
                class="glyphicon glyphicon-search"></i></button>
    </div>

    <form class="well" role="form " style="width: 400px;margin-top: 40px;" ng-show="form_search">
        <fieldset>
            <legend>Поиск</legend>
            <div class="form-group" ng-if="show_select_type()">
                <label for="inputstatus">Тип:</label>
                <select ui-select2 ng-model="get.type_id" ng-change="select_type(get.type_id)" style="width: 200px;">
                    <option value="0">Все</option>
                    <option value="{{type.id}}" ng-repeat="type in types">{{type.name}}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="inputstatus">Категория:</label>
                <select ui-select2 ng-model="get.cid" style="width: 100%">
                    <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                            ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="inputname">Название коллекции</label>
                <input ng-model="get.name" class="form-control" id="inputname" placeholder="">
            </div>
            <div class="form-group">
                <label for="inputstep">Кол-во товаров на стр.</label>
                <input ng-model="get.step" class="form-control" id="inputstep" placeholder="20">
            </div>
            <button ng-click="search()" type="button" class="btn btn-success">Искать</button>
            <button ng-click="cancel()" type="button" class="btn btn-default">Отмена</button>
        </fieldset>
    </form>

    <br clear="all"/>
    <br/>

    <div>
        <div>Кол-во кол-ций: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover table-middle">
        <tr>
            <th class="i-center-td" width="20">ID</th>
            <th class="i-center-td" width="55">Фото</th>
            <th>Название</th>
            <th width="100">Категория</th>
            <th class="i-center-td" width="75">
                <i title="Приоритет" class="glyphicon glyphicon-sort-by-attributes-alt"></i>
            </th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in collections">
            <td class="i-center-td">{{item.id}}</td>
            <td>
                <label class="b-input-upload" for="upload_{{item.id}}">
                    <img width="50" ng-src="{{getImageSrc(item.id)}}" alt=""/>
                    <input title="Загрузка фото" id="upload_{{item.id}}" type="file" ng-file-select="onFileSelect($files,item.id)"/>
                </label>
            </td>
            <td>
                <a href="<?= link_to('admin_collection', array('action' => 'edit')) ?>?id={{item.id}}">
                    <span ng-show="item.status == 1"> {{item.name}}</span>
                    <s ng-show="item.status == 0">{{item.name}}</s>
                </a>
            </td>
            <td>
                {{getNameCategory(item.cid)}}
            </td>
            <td class="i-center-td">
                <input ng-model="item.pos" style="width: 20px; text-align: center;" ng-blur="save(item)" class="pos"/>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_collection', array('action' => 'edit')) ?>?id={{item.id}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="delete(item.id)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>


    <?= $pagination; ?>

</div>

<script type="text/javascript">
    window._types = <?= $types? \Delorius\Utils\Json::encode((array)$types): '[]'?>;
    window._collections = <?= $collections? \Delorius\Utils\Json::encode((array)$collections): '[]' ?>;
    window._get = <?= $get? \Delorius\Utils\Json::encode((array)$get): '{cid:0,type_id:0}' ?>;
    window._images = <?= $images? \Delorius\Utils\Json::encode((array)$images): '[]' ?>;
    window._collection_categories = <?= $collection_categories? \Delorius\Utils\Json::encode((array)$collection_categories): '[]' ?>;
    window._categories = <?= $this->action('Shop:Admin:Category:catsJson',array('pid'=>0,'typeId'=>($get['type_id'])? (int)$get['type_id']:\Shop\Catalog\Entity\Category::TYPE_GOODS,'placeholder'=>'Все категории'));?>;
</script>