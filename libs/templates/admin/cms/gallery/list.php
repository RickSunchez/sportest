<div ng-controller="GalleriesListController" ng-init="init()">
    <div class="clearfix">
        <a title="Добавить галерею" class="btn btn-info btn-xs"
           href="<?= link_to('admin_gallery', array('action' => 'add')) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>

    <? if ($multi): ?>
        <div class="b-selects">
            <label for="domains">Выберите сайт:</label>
            <select id="domains" ui-select2 ng-model="select_domain" ng-change="select()">
                <option value="{{d.name}}" ng-repeat="d in domain">{{d.host}}</option>
            </select>
        </div>
    <? endif ?>

    <br/>
    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Название</th>
            <th>Описание</th>
            <th ng-if="show_cat()">Категория</th>
            <? if ($multi): ?>
                <th>Домен</th>
            <? endif; ?>
            <th width="80">Приоритет</th>
            <th width="20">#</th>
        </tr>
        <tr ng-repeat="item in galleries">
            <td>{{item.gallery_id}}</td>
            <td class="i-center-td">
                <span ng-if="item.status == 1">
                    <i ng-click="status(item.gallery_id,0)" class="glyphicon glyphicon-eye-open"
                       style="cursor: pointer;color: green;"></i>
                </span>
                <span ng-if="item.status == 0">
                    <i ng-click="status(item.gallery_id,1)" class="glyphicon glyphicon-eye-close"
                       style="cursor: pointer;"></i>
                </span>
            </td>
            <td>
                <a tabindex="-1"
                   href="<?= link_to('admin_gallery', array('action' => 'images')) ?>?id={{item.gallery_id}}">
                    {{item.name}}
                </a>
            </td>
            <td>{{item.note}}</td>
            <td ng-if="show_cat()">{{getNameCat(item.cid)}}</td>
            <? if ($multi): ?>
                <td>{{item.site}}</td>
            <? endif; ?>
            <td class="i-center-td">
                <input ng-model="item.pos" class="pos" ng-change="change(item)"/>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a tabindex="-1"
                               href="<?= link_to('admin_gallery', array('action' => 'images')) ?>?id={{item.gallery_id}}">
                                Загрузить фото
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1"
                               href="<?= link_to('admin_gallery', array('action' => 'archive')) ?>?id={{item.gallery_id}}">
                                Получить фото архивом
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:void(0);"
                               ng-click="delete(item)">Удалить</a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
    <?= $pagination->render(); ?>
</div>

<script type="text/javascript">
    window._galleries = <?= $galleries ? \Delorius\Utils\Json::encode((array)$galleries) : '[]'?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson', array('pid' => 0, 'typeId' => \CMS\Catalog\Entity\Category::TYPE_GALLERY, 'placeholder' => 'Без категории'));?>;
    window._domain = <?= $domain ? \Delorius\Utils\Json::encode((array)$domain) : '{}'?>;
    window._select_domain = '<?= $get['domain'] ? $get['domain'] : 'www'?>';
</script>