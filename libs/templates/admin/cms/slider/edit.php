<div ng-controller="SliderEditCtrl" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_slider', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Слайдер</h1>
    <form class="form-horizontal well" role="form">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" ng-model="slider.status" ng-true-value="'1'"
                               id="inputstatus" ng-false-value="'0'"/> Опубликовать
                    </label>
                </div>
            </div>
        </div>

        <div ng-if="slider.id">
            <div class="form-group">
                <label class="col-sm-2 control-label" for="image">Загрузка</label>

                <div class="col-sm-10">
                    <input type="file" ng-file-select="onFileSelect($files,slider.id)">
                </div>
            </div>

            <div class="form-group" ng-if="image.image_id">
                <label class="col-sm-2 control-label" for="image">Сладер</label>

                <div class="col-sm-10">
                    <img ng-src="{{image.preview}}" alt="" width="100px"/>
                    <a href="<?= link_to('admin_image',array('action'=>'edit'))?>?id={{image.image_id}}"
                       title="Редактировать" class="btn btn-info btn-xs" >
                        <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="title">Код</label>

            <div class="col-sm-3">
                <input type="text" id="title" ng-model="slider.code" class="form-control"
                       placeholder="Код слайдера"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="title">Заголовок</label>

            <div class="col-sm-10">
                <input type="text" id="title" ng-model="slider.title" class="form-control"
                       placeholder="Зоголовок"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="url">Ссылка</label>

            <div class="col-sm-10">
                <input type="text" id="url" ng-model="slider.url" class="form-control"
                       placeholder="Ссылка перехода"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="pos">Позиция</label>

            <div class="col-sm-3">
                <input type="text" id="pos" ng-model="slider.pos" class="form-control"/>
                <span class="help-block">Не обязательное поле</span>
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
    window._image = <?= $image ? \Delorius\Utils\Json::encode($image) : 'null' ;?>;
    window._slider = <?= $slider? \Delorius\Utils\Json::encode((array)$slider): '{status:"0"}'?>;
</script>




