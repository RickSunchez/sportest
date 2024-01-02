<div ng-controller="OrmRoleCtrl" ng-init="init()" >


    <table class="table table-striped table-bordered table-hover table-condensed">
        <tr>
            <td>Название</td>
            <td class="text-center" title="Create">Create</td>
            <td class="text-center" title="Read">Read</td>
            <td class="text-center" title="Update">Update</td>
            <td class="text-center" title="Delete">Delete</td>
        </tr>
        <tr ng-repeat="orm in list" >
            <td>
                <b>{{orm.object_name}}</b> [{{orm.table_name}}]
            </td>
            <td>
               <select name="create" ng-model="orm.action['create']" ng-change="change(orm,'create')" >
                   <option value="-1">Ignore</option>
                   <option value="0" >Deny</option>
                   <option value="1" >Allow</option>
               </select>
            </td>
            <td>
                <select name="read" ng-model="orm.action['read']" ng-change="change(orm,'read')">
                    <option value="-1">Ignore</option>
                    <option value="0" >Deny</option>
                    <option value="1" >Allow</option>
                </select>
            </td>
            <td>
                <select name="update" ng-model="orm.action['update']" ng-change="change(orm,'update')">
                    <option value="-1">Ignore</option>
                    <option value="0" >Deny</option>
                    <option value="1" >Allow</option>
                </select>
            </td>
            <td>
                <select name="delete" ng-model="orm.action['delete']" ng-change="change(orm,'delete')">
                    <option value="-1">Ignore</option>
                    <option value="0" >Deny</option>
                    <option value="1" >Allow</option>
                </select>
            </td>
        </tr>
        </table>

</div>

<script type="text/javascript">
    window._orms = <?= \Delorius\Utils\Json::encode($orms);?>;
    window._acl = <?= \Delorius\Utils\Json::encode($acl);?>;

    window._code = '<?= $code;?>';
    window._type = '<?= $type;?>';
</script>