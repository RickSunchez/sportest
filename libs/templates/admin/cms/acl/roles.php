<div ng-controller="RolesCtrl" ng-init="init()">

    <h1>Тип ролей: <?= $type?></h1>

    <div class="roles_list" ng-repeat="role in getRoles(0)" ng-include="'tpl_block'"></div>

    <script type="text/ng-template" id="tpl_block">
        <div class="clearfix item ">
            <div class="btn-group fl_r" ng-hide="role.is_root == 1" >
                <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="glyphicon glyphicon-cog"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li><a href="<?= link_to('admin_acl',array('action'=>'orm','type'=>$type));?>&role={{role.code}}" target="_blank">
                        <i class="glyphicon glyphicon-share-alt"></i>
                        Права доступы к ORM
                    </a></li>
                </ul>
            </div>

            <div class="name">{{role.name}} ({{role.code}})</div>

        </div>
        <div class="child" ng-repeat="role in getRoles(role.role_id)" ng-include="'tpl_block'"></div>
    </script>

</div>

<style type="text/css">
    .roles_list .child {
        margin-left: 10px;
    }

    .roles_list .item .name {
        float: left;
    }

    .roles_list .item {
        padding: 5px 5px;
        border: 1px solid #ccc;
        margin: 1px 0;
    }

    .roles_list .item:hover {
        background: #f5f5f5;
    }
</style>


<script type="text/javascript">
    window._roles = <?= \Delorius\Utils\Json::encode($roles);?>;
    window._type = '<?= $type;?>';
</script>