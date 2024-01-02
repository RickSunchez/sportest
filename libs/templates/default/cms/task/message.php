<div ng-controller="HelpDeskMessageCtrl" ng-init="init()">
    <form>
        <textarea ng-model="form.text" placeholder="комментарий"/></textarea>
        <a href="#" ng-click="send()">send</a>
    </form>
</div>

<script>
    window._task = <?= \Delorius\Utils\Json::encode($task->as_array());?>;
</script>

<table>
    <? foreach ($messages as $message): ?>
        <tr>
            <td>#<?= $message->pk()?></td>
        </tr>
    <? endforeach ?>
</table>