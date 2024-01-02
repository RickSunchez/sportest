<div ng-controller="GoodsOptionVariantEditCtrl" ng-init="init()">

    <div class="clearfix btn-group ">
        <a title="Назад к товару" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'edit', 'id' => $goods['goods_id'])) ?>">
            <i class="glyphicon glyphicon-arrow-left"></i> Назад к товару
        </a>
        <a title="Назад к опции" class="btn btn-info btn-xs"
           href="<?= link_to('admin_option', array('action' => 'edit', 'id' => $option['id'])) ?>">
            <i class="glyphicon glyphicon-arrow-left"></i> Назад к опции
        </a>

    </div>

    <br/>

    <h1>Вариант</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li><a href="<?= link_to('admin_option', array('action' => 'edit', 'id' => $option['id'])) ?>">Опция</a></li>
        <li class="active"><a href="javascript:;" data-toggle="tab">Редактирование варианта</a></li>
    </ul>

    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <div class="checkbox">
                <label class="col-sm-offset-2 col-sm-10" for="inputstatus">
                    <input type="checkbox" ng-model="variant.status" ng-true-value="'1'" id="inputstatus"
                           ng-false-value="'0'"/> <b>Показать</b>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="image">Загрузка</label>

            <div class="col-sm-10">
                <input type="file" ng-file-select="onFileSelect($files,variant.id)">
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

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="variant.name" class="form-control"
                       placeholder="Заголовок баннера"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="modifier">Модификатор</label>

            <div class="col-sm-4">
                <input style="text-align: right;width: 100px;" type="text" id="modifier" ng-model="variant.modifier" class="form-control" placeholder="Код"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="pos">Приоритет</label>

            <div class="col-sm-10">
                <input type="text" id="pos" ng-model="variant.pos" class="form-control"
                       style="text-align: right;width: 100px;"
                       placeholder="0" parser-int/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="external_id">Код</label>

            <div class="col-sm-4">
                <input type="text" id="external_id" ng-model="variant.external_id" class="form-control" placeholder="Внешний ID"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Описание</label>

            <div class="col-sm-10">
                <textarea id="text" ng-model="variant.text" class="form-control"></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="comment">Комментарий</label>

            <div class="col-sm-10">
                <input type="text" id="comment" ng-model="variant.comment" class="form-control"/>

                <p class="help-block">Введите комментарий, который будет показываться под опцией</p>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._variant = <?= $variant? \Delorius\Utils\Json::encode((array)$variant): '{}'?>;
    window._option = <?= $option? \Delorius\Utils\Json::encode((array)$option): '{}'?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): '[]'?>;
</script>




