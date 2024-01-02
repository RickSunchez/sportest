<h1>Заявка по теме: <?=\CMS\Core\Helper\Jevix\JevixEasy::Parser($task->subject)?></h1>
<?if($task->status != 3):?>
<div ng-controller="HelpDeskMessageCtrl" ng-init="init()">
    <form class="form-horizontal well" role="form">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Сообщение</label>
            <div class="col-sm-10">
                <textarea ng-model="form.text" id="text" name="text" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="send()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>
</div>
<? endif ?>
<div ng-controller="HelpDeskMsgListCtrl" ng-init="init()">

    <table class="table table-condensed table-bordered table-hover">
        <tr ng-repeat="item in msgs" ng-class="{admin_message:item.is_admin==1}" >
            <td >
                <span ng-if="item.is_admin == 0" >Вы писали</span>
                <span ng-if="item.is_admin == 1" >Администрация</span>
            </td>
            <td ng-bind-html="as_html(item.html)" ></td>
            <td>{{item.created}}</td>
        </tr>
        <tr class="<?=($task->is_admin == 1) ? 'admin_message' : null?>">
            <td style="width: 150px">
                <? if( $task->is_admin == 1): ?>
                    Администрация
                <? else: ?>
                    Вы писали
                <? endif ?>
            </td>
            <td>
                <?=\CMS\Core\Helper\Jevix\JevixEasy::Parser($task->text)?>
            </td>
            <td style="width: 150px"><?=date('d.m.Y H:i', $task->date_cr)?></td>
        </tr>
    </table>
    <?=$pagination->render()?>
</div>



<script>
    window._task = <?= \Delorius\Utils\Json::encode($task->as_array());?>;
    window._messages = <?= $messages ? \Delorius\Utils\Json::encode((array)$messages): '[]' ?>;
</script>