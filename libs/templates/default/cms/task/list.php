<div ng-controller="HelpDeskCtrl">
    <form>
        <input type="text" ng-model="form.subject" placeholder=""/>
        <textarea ng-model="form.text" placeholder="комментарий"/></textarea>
        <a href="#" ng-click="send()">send</a>
    </form>
</div>

<table>
    <? foreach ($task as $t): ?>
        <tr>
            <td>#<?= $t->pk()?></td>
            <td><a href="<?= link_to('help_desk_show',array('id'=>$t->pk()))?>"><?= $t->subject?></a></td>
            <td><?= $t->getNameStatus()?></td>
            <td><?= $t->getNameTypes()?></td>
        </tr>
    <? endforeach ?>
</table>