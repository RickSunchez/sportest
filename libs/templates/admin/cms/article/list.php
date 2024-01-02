<div ng-controller="ArticleListCtr" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_article', array('action' => 'add')) ?>"
           title="Добавить новость">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br clear="all"/>


    <p>Кол-во статей: <?= $pagination->getItemCount() ?></p>


    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Название</th>
            <th ng-if="show_cat()">Категория</th>
            <th width="200">Дата</th>
            <th width="20"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in articles">
            <td class="i-center-td">{{item.id}}</td>
            <td class="i-center-td">
                <span ng-if="item.status == 1"><i ng-click="status(item.id,0)" class="glyphicon glyphicon-eye-open"
                                                  style="cursor: pointer;color: green;"></i></span>
                <span ng-if="item.status == 0"><i ng-click="status(item.id,1)" class="glyphicon glyphicon-eye-close"
                                                  style="cursor: pointer;"></i></span>
            </td>
            <td><a href="<?= link_to('admin_article', array('action' => 'edit')) ?>?id={{item.id}}">{{item.name}}</a>
            </td>
            <td class="i-middle-td" ng-if="show_cat()">{{getNameCat(item.cid)}}</td>
            <td>{{item.created}}</td>
            <td>{{item.views}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="fa fa-list"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_article', array('action' => 'edit')) ?>?id={{item.id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.id)">
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
    window._articles = <?= $articles ? \Delorius\Utils\Json::encode((array)$articles) : '[]' ?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson', array('pid' => 0, 'typeId' => \CMS\Catalog\Entity\Category::TYPE_ARTICLE, 'placeholder' => 'Без категории'));?>;
    window._select_domain = '<?= $get['domain'] ? $get['domain'] : 'www'?>';
    window._domain = <?= $domain ? \Delorius\Utils\Json::encode((array)$domain) : '{}'?>;
</script>


