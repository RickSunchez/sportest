<div ng-controller="GoodsReviewsListCtrl" ng-init="init()">

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20" style="white-space: nowrap; text-align: center;"><i class="glyphicon glyphicon-eye-open" style="color: #428bca" title="Видимость элемента" ></i></th>
            <th width="100">Автор</th>
            <th>Товар</th>
            <th width="150">Дата</th>
            <th width="20">Оценка</th>
            <th width="10"></th>
        </tr>
        <tr ng-repeat="item in reviews">
            <td>{{item.review_id}}</td>
            <td>
                <span ng-hide="item.status" ng-click="status(item.review_id, 1)" style="cursor: pointer;">
                    <i class="glyphicon glyphicon-eye-close" style="cursor: pointer; color: #FF0000 " title="Показать отзыв на сайте" ></i>
                </span>
                 <span ng-show="item.status" ng-click="status(item.review_id, 0)" style="cursor: pointer;">
                    <i class="glyphicon glyphicon-eye-open" style="cursor: pointer; color: #66AA66" title="Не показывать отзыв на сайте" ></i>
                </span>
            </td>
            <td><a title="Редактировать"
                   href="<?= link_to('admin_user', array('action' => 'edit')) ?>?id={{item.user_id}}">{{users[item.user_id].email}}</a></td>
            <td><a href="<?= link_to('admin_goods', array('action' => 'edit')) ?>?id={{item.goods_id}}">{{goods[item.goods_id].name}}</a></td>
            <td style="white-space: nowrap; text-align: center;">{{item.created}}</td>
            <td style="white-space: nowrap; text-align: center;"><strong>{{item.rating}}</strong></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_reviews', array('action' => 'edit')) ?>?id={{item.review_id}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="delete(item.review_id)">
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
<script type="application/javascript">
    window._reviews = <?= $reviews ? \Delorius\Utils\Json::encode((array)$reviews): '[]' ?>;
    window._users   = <?= $users ? \Delorius\Utils\Json::encode((array)$users): '[]' ?>;
    window._goods   = <?= $goods ? \Delorius\Utils\Json::encode((array)$goods): '[]' ?>;
</script>