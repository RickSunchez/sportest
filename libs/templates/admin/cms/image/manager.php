<div ng-controller="ImagesManagerCtrl" ng-init='init()'>

    <form class="form-inline well ">
        <div class="form-group" style="margin-right: 20px;">
            <label for="name">Категория: </label>
            <select ui-select2 name="table" ng-model="get.table" style="width: 300px;" ng-change="search()">
                <option value="">Все изображения</option>
                <option value="{{t.target_name}}"
                        ng-repeat="t in types">
                    {{t.target_name}}[{{t.target_type}}]&nbsp;&nbsp;&nbsp;({{t.count}})
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="id">ID источника: </label>
            <input ng-model="get.image_id" type="text" class="form-control" id="id" placeholder="{ID}"
                   style="width:100px !important;margin-right: 10px;" ng-change="search()">
        </div>
    </form>

    <header class="b-manager__header">
        <h1 ng-show="get.table">Таблица: {{get.table}}</h1>
    </header>

    <ul class="b-manager__list">
        <li class="b-manager__item" ng-repeat="image in images" ng-dblclick="select(image)">
            <i title="Удалить" class="glyphicon glyphicon-trash" ng-click="delete(image)"></i>

            <img ng-src="{{image.preview}}" src="/source/images/no.png" alt=""/>

            <div class="name">
                Size: {{image.width}}x{{image.height}}
            </div>

        </li>

    </ul>

    <div class="b-manager__msg" ng-show="images.length == 0">
        В категории "{{get.name}}" нет картинок, попробуйте выбрать другую категорию
    </div>

    <div style="text-align: center">
        <?= $pagination->render(); ?>
    </div>
</div>

<script type="text/javascript">
    window._images = <?= $images? \Delorius\Utils\Json::encode((array)$images): '[]' ?>;
    window._types = <?= $types? \Delorius\Utils\Json::encode((array)$types): '[]' ?>;
    window._get = <?= $get? \Delorius\Utils\Json::encode((array)$get): '{}' ?>;
</script>


