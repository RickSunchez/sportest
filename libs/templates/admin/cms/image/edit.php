<div ng-controller="ImageEditCtrl" ng-init='init("#jcrop_target")'>

    <h1>Редактирования изображения</h1>

    <div>
        <a href="javascript:history.back();" class="btn btn-danger btn-xs">Назад</a>
    </div>

    <br/>
    <br/>

    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="image.name" class="form-control"
                       placeholder="Заголовок баннера"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Описание</label>

            <div class="col-sm-10">
                <textarea style="height: 200px;" name="text" id="text" ng-model="image.text" class="form-control"
                                  placeholder="Описание картинки"></textarea>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>

    <br/>
    <br/>

    <table class="table table-condensed table-bordered table-hover table-middle">
        <tr>
            <th width="30" class="i-center-td">ID</th>
            <th width="40" class="i-center-td">target_id</th>
            <th width="200">target_type</th>
            <th>Size normal</th>
            <th>Size preview</th>
            <th>Snipped</th>
        </tr>
        <tr>
            <td  class="i-center-td">{{image.image_id}}</td>
            <td class="i-center-td">{{image.target_id}}</td>
            <td>{{image.target_type}}</td>
            <td>{{image.width}}x{{image.height}}</td>
            <td>{{image.pre_width}}x{{image.pre_height}}</td>
            <td>[[image:{{image.image_id}}]]<br />[[image:{{image.image_id}}?thumb]]</td>
        </tr>
    </table>

    <div class="b-image__preview">
        <div class="b-image__title">Preview image {{image.pre_width}}x{{image.pre_height}}</div>
        <img ng-src="{{image.preview}}" alt=""/>
    </div>

    <form class="b-image__size">
        <div style="width:0px;height: 0px;overflow: hidden;float:left;">
            <input size="4" id="x" name="x" ng-model="coords.x" type="text">
            <input size="4" id="y" name="y" ng-model="coords.y" type="text">
            <input size="4" id="x2" name="x2" ng-model="coords.x2" type="text">
            <input size="4" id="y2" name="y2" ng-model="coords.y2" type="text">
        </div>
        <label>W <input size="4" id="w" name="w" type="text" ng-model="coords.w"></label>
        <label>H <input size="4" id="h" name="h" type="text" ng-model="coords.h"></label>
        <label for="">
            Resize {{image.pre_width}}x{{image.pre_height}}
            <input type="checkbox" ng-model="coords.resize" ng-true-value="'1'"  ng-false-value="'0'"/>
        </label>
        <br />
        <a href="javascript:;" class="btn btn-xs btn-info" ng-click="crop()">Изменить</a>
        <a title="Обновить данные по изображению с сервера" href="javascript:;" class="btn btn-xs btn-danger" ng-click="refresh()"><i class="glyphicon glyphicon-refresh"></i></a>
    </form>

    <div class="b-image__normal">
        <div class="b-image__title">Normal image</div>
        <img id="jcrop_target" ng-src="{{image.normal}}" alt=""/>
    </div>

</div>

<script type="text/javascript">
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): '[]' ?>;
    window._get = <?= $get? \Delorius\Utils\Json::encode((array)$get): '[]' ?>;
</script>


