<div ng-controller="GoController" ng-init="init()">
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="javascript:void(0);" ng-click="open_form_add()">Добавить ссылку</a>
    </div>

    <div class="alert alert-error" ng-if="errors.length">
        <ul>
            <li ng-repeat="error in errors">{{error}}</li>
        </ul>
    </div>


    <div ng-show="add_form">
        <br/><br/>

        <form class="form-inline well clearfix" role="form">
            <div class="form-group col-sm-10">Добавить новую ссылку</div>
            <div class="form-group col-sm-10">
                <input type="text" ng-model="new_go.redirect" class="form-control" placeholder="URL"  style="width: 100%;" />
            </div>
            <br/>
            <button type="submit" class="btn btn-info " ng-click="add()">Добавить</button>

            <div class="form-group col-sm-10">
                <br/>
                <textarea ng-model="new_go.comment" style="height: 50px;width: 100%;" class="form-control"
                          placeholder="Комментарий по ссылке"></textarea>
            </div>
        </form>
    </div>

    <div ng-show="edit_form">
        <br/><br/>

        <form class="form-inline well">
            <fieldset>
                <legend>Редактирование ссылки</legend>
                <div class="form-group col-sm-10">
                    <p class="form-control-static" style="font-weight: bold;">Ссылка: {{ getUrl(edit_go.url) }}</p>
                </div>
                <div class="form-group col-sm-10">
                    <p class="form-control-static" style="font-weight: bold;">Визитов: {{edit_go.visit}}</p>
                </div>

                <div class="form-g  roup col-sm-10">
                    <br/><br/>
                </div>

                <div class="form-group col-sm-10">
                    <label>Ссылка на страниц</label>
                    <input type="text" ng-model="edit_go.redirect" class="form-control" placeholder="URL">
                </div>

                <div class="form-group col-sm-10">
                    <label>Примечание</label>
                    <textarea ng-model="edit_go.comment" style="height: 40px;" class="form-control"
                              placeholder="Комментарий по ссылке"></textarea>
                </div>

                <div class="form-group col-sm-10">
                    <br/><br/>
                    <button type="button" class="btn  btn-success" ng-click="save()">Сохранить</button>
                    <button type="button" class="btn  btn-danger" ng-click="reset()">Обнулить статистику</button>
                </div>
            </fieldset>
        </form>
    </div>

    <br/>
    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th>URL</th>
            <th>Redirect</th>
            <th>Визиты</th>
            <th>примечание</th>
            <th width="20">#</th>
        </tr>
        <tr ng-repeat="item in go">
            <td><a target="_blank" href="{{ getUrl(item.url) }}">{{ getUrl(item.url) }}</a></td>
            <td>{{item.redirect}}</td>
            <td>{{item.visit}}</td>
            <td>{{item.comment}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li><a tabindex="-1" href="javascript:void(0);"
                               ng-click="open_form_edit(item)">Редактировать</a></li>
                        <li><a tabindex="-1"
                               href="<?= link_to('admin_go', array('action' => 'delete')) ?>?id={{item.go_id}}">Удалить</a>
                        </li>
                        <li><a tabindex="-1"
                               href="<?= link_to('admin_go', array('action' => 'stat')) ?>?id={{item.go_id}}">Статистика</a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
    <?= $pagination->render(); ?>
</div>

<script type="text/javascript">
    window._go = <?= \Delorius\Utils\Json::encode($go)?>;
</script>