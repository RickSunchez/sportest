<div ng-controller="TemplateController" ng-init="init()">
    <div class="btn-group">
        <a href="<?= link_to('admin_tmp', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_tmp', array('action' => 'add')) ?>"
           title="Добавить шаблон">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br/>

    <h1>Шаблон</h1>


    <form class="form-horizontal well top-border" role="form">


        <div ng-show="tmp.count">

            <div class="form-group">
                <label class="col-sm-2 control-label" for="count">Кол-во шаблон</label>

                <div class="col-sm-3">
                    <input type="text" id="count" ng-model="tmp.count" class="form-control"/>
                    <span class="help-block">шаблоны</span>
                </div>
                <div class="col-sm-3">
                    <input type="text" id="step" ng-model="tmp.step" class="form-control"/>
                    <span class="help-block">мах попытка</span>
                </div>
            </div>

        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="tmp.name" class="form-control"
                       placeholder="Название шаблона"/>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Текст</label>

            <div class="col-sm-10">
                <textarea style="height: 300px;"
                          name="text" id="text" ng-model="tmp.text" class="form-control"
                          placeholder="Текст шаблона [var1|var2|...]"></textarea>
                <span class="help-block">
                    <b>Пример:</b> [[Сегодня [утром|после обеда]]|Вчера] я [побежал|пошел|поехал[ на автобусе| на машине| на [трамвае|троллейбусе]|]]<br/>
                    <b>Шаблон кода:</b> {+page:1?child+} = &#91;page:1?child&#93;
                </span>
            </div>
        </div>

        <div ng-if="text" class="form-group">
            <label class="col-sm-2 control-label">Пример текста</label>

            <div style="padding-top: 7px;" class="col-sm-10" data-ng-bind-html="text | to_html">

            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="btn-group">
                    <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
                    <a ng-if="tmp.id" title="Показать случайны пример" ng-click="example(tmp)"
                       class="btn btn-warning"><i
                            class="glyphicon glyphicon-eye-open"></i></a>
                    <a ng-if="tmp.id" title="Сгенерировать шаблоны" ng-click="getAll(tmp)"
                       class="btn btn-primary"><i
                            class="glyphicon glyphicon-cog"></i> {{count}}</a>

                </div>
            </div>
        </div>
    </form>

</div>

<script type="text/javascript">
    window._tmp = <?= $tmp? \Delorius\Utils\Json::encode((array)$tmp): '{}'?>;
    window._count = <?= $count? $count: 'null'?>;
</script>




