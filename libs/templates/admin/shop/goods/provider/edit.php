<div ng-controller="ProviderEditController" ng-init="init()">

    <div class="clearfix btn-group ">
        <a title="Назад" class="btn btn-danger btn-xs" href="<?= link_to('admin_provider', array('action' => 'list')) ?>">
            <i class=" glyphicon glyphicon-arrow-left"></i>
        </a>
    </div>

    <h1>Поставщик</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#provider" data-toggle="tab">Описание</a></li>
    </ul>


    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="provider">


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="provider.name" class="form-control"
                               placeholder="Название"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text">Описание</label>

                    <div class="col-sm-10">
                        <textarea id="text" ng-model="provider.text" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Позиция</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="provider.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Необязательное поле</span>
                    </div>
                </div>
            </div>
            <!-- #vendor -->

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._provider = <?= $provider? \Delorius\Utils\Json::encode((array)$provider): '{}'?>;
</script>





