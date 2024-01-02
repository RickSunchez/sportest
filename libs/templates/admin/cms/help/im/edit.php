<div ng-controller="AddHelpCtrl" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_page', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>


    <h1>Добавить задачу</h1>

    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="title">Тип заявки</label>

            <div class="col-sm-10">
                <input type="text" id="title" ng-model="page.title" class="form-control" placeholder="Title"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="short_title">Короткое название</label>

            <div class="col-sm-10">
                <input type="text" id="short_title" ng-model="page.short_title" class="form-control"
                       placeholder="BreadCrumbs"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

            <div class="col-sm-10">
                <input type="text" id="url" ng-model="page.url" class="form-control"/>
                <span class="help-block">Генерируется автоматически</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Текст</label>

            <div class="col-sm-10">
                <textarea name="text" id="text" ng-model="page.text" class="form-control"></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="pos">Позиция</label>

            <div class="col-sm-10">
                <input type="text" id="pos" ng-model="page.pos" class="span1"/>
                <span class="help-block">Не обязательное поле</span>
            </div>
        </div>


        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
        </div>

    </form>

</div>




