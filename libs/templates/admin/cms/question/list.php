<div ng-controller="QuestionsListCtrl" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_question', array('action' => 'add')) ?>"
           title="Добавить отзыв">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br clear="all"/>


    <p>Кол-во вопросов: <?= $pagination->getItemCount() ?></p>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Данные пользователя</th>
            <th>Вопрос</th>
            <th width="200">Дата</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in questions">
            <td class="i-center-td">{{item.id}}</td>
            <td class="i-center-td">
                <span ng-if="item.status == 1">
                    <i ng-click="status(item.id,0)"
                       class="glyphicon glyphicon-eye-open"
                       style="cursor: pointer;color: green;"></i>
                </span>
                <span ng-if="item.status == 0">
                    <i ng-click="status(item.id,1)"
                       class="glyphicon glyphicon-eye-close"
                       style="cursor: pointer;"></i>
                </span>
            </td>
            <td>
                <div ng-if="item.name">Имя: {{item.name}}</div>
                <div ng-if="item.phone">Телефон: {{item.phone}}</div>
                <div ng-if="item.email">E-mail: {{item.email}}</div>
                <div ng-if="item.contact">Контакт: {{item.contact}}</div>
            </td>
            <td>{{item.text}}</td>
            <td>{{item.created}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_question', array('action' => 'edit')) ?>?id={{item.id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать
                            </a>
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
    <?= $pagination->render(); ?>

</div>

<script type="text/javascript">
    window._questions = <?= $questions? \Delorius\Utils\Json::encode((array)$questions): '{}' ?>;
</script>


