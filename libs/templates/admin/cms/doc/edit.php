<div ng-controller="DocumentController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_doc', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Редактирование файла</h1>

    <form class="form-horizontal well" role="form">

        <div ng-if="show_cat()" class="form-group">
            <label for="inputstatus" class="col-sm-2 control-label">Раздел</label>

            <div class="col-sm-10">
                <p class="form-control-static">
                    <select ui-select2 name="cid" ng-model="file.cid" style="width: 100%;">
                        <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                        </option>
                    </select>
                </p>

            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="file.title" class="form-control"
                       placeholder="Название файла"/>
            </div>
        </div>

        <div class="form-group">
            <div>

            </div>
            <label class="col-sm-2 control-label" for="file">Заменить:</label>

            <div class="col-sm-10">
                <input id="file" type="file" ng-file-select="onFileSelect($files, file)"><br/>
                <img ng-show="imageExtensions.indexOf( file.ext ) != -1" ng-src="{{file.path}}" width="200"/>
                <a ng-show="imageExtensions.indexOf( file.ext ) == -1"
                   href="<?= link_to('doc_download', array('id' => $file['file_id'])) ?>">
                    Скачать: {{file.file_name}}
                </a>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="visit">Скачиваний</label>

            <div class="col-sm-3">
                <div class="input-group">
                    <input disabled type="text" id="visit" ng-model="file.count" class="form-control"
                           placeholder="0"/>

                            <span class="input-group-btn">
                                <button ng-click="file.count = 0" class="btn btn-default" type="button">Обнулить
                                </button>
                            </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._file = <?= $file ? \Delorius\Utils\Json::encode((array)$file ): '{count:0}'?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson',array('pid'=>0,'typeId'=>\CMS\Catalog\Entity\Category::TYPE_DOCS,'placeholder'=>'Без категории'));?>;
</script>




