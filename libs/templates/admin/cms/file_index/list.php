<div ng-controller="FileIndexListController" ng-init="init()">

    <div class="clearfix">
        Добавить файл: <input type="file" ng-file-select="onFileSelect($files)" multiple/>
    </div>
    <br clear="all"/><br clear="all"/>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th>Файл</th>
            <th>Дата</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="file in files">
            <td>{{file.file_id}}</td>
            <td>{{file.file_name}}</td>
            <td>{{file.created}}</td>
            <td>
                <a href="javascript:;" ng-click="delete(file)">
                    <i class="glyphicon glyphicon-trash"></i>
                </a>
            </td>
        </tr>
    </table>
</div>

<script type="application/javascript">
    window._files = <?= $files? \Delorius\Utils\Json::encode($files): '[]' ?>;
</script>