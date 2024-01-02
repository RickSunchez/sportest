<div ng-controller="PollEditController" ng-init="init()" >

    <h1>Опрос</h1>

    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название</label>

            <div class="col-sm-10">
                <input  name="name" id="name" ng-model="poll.name" class="form-control" placeholder="Заголовок" />
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Вопрос</label>

            <div class="col-sm-10">
                <textarea name="text" id="text" ng-model="poll.text" class="form-control" placeholder="Текст опроса"/></textarea>
            </div>
        </div>
       

        <div class="form-group">
            <div class="col-sm-8">
                <p class="form-control-static">Вариант ответа</p>
            </div>
            <div class="col-sm-1">
                <p class="form-control-static">Голосов</p>
            </div>
            <div class="col-sm-1">
                <p class="form-control-static">Позиция</p>
            </div>
        </div>

        <div ng-hide="item.delete" id="color_inc_{{item.inc}}" class="form-group" ng-repeat="item in items">

            <div class="col-sm-8">
                <input type="text" id="name_{{item.inc}}" ng-model="item.name" class="form-control"
                       placeholder="Значение"/>
            </div>
            <div class="col-sm-1">
                <input parser-int type="text" id="count_{{item.inc}}" ng-model="item.count" class="form-control"
                       placeholder="0"/>
            </div>
            <div class="col-sm-1">
                <input parser-int type="text" id="pos_{{item.inc}}" ng-model="item.pos" class="form-control"
                       placeholder="0"/>
            </div>

            <div class="col-sm-1">
                <a ng-click="deleteItem(item.inc)" class="btn btn-danger" href="javascript:void(0);">
                    <i class="glyphicon glyphicon-trash"></i></a>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                <a href="javascript:void(0);" ng-click="addItem()" class="btn btn-success btn-xs">
                    <i class="glyphicon glyphicon-plus"></i>
                    Добавить вариант ответа
                </a>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._poll = <?= $poll? \Delorius\Utils\Json::encode((array)$poll): '{}' ?>;
    window._items = <?= $items? \Delorius\Utils\Json::encode((array)$items): '[]' ?>;
</script>


