<div ng-controller="BannersController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_banner', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Баннер</h1>

    <form class="form-horizontal well" role="form">

        <span ng-show="banner.banner_id">

             <div class="form-group">
                 <div class="checkbox">
                     <label class="col-sm-offset-2 col-sm-10">
                         <input type="checkbox" ng-model="banner.status" ng-true-value="'1'" id="inputstatus"
                                ng-false-value="'0'"/> <b>Показать баннер</b>
                     </label>
                 </div>
             </div>

        </span>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="type">Тип</label>

            <div class="col-sm-10">

                <select class="form-control" ng-model="banner.type_id" ng-hide="banner.banner_id">
                    <option value="">-Выберите-</option>
                    <option ng-repeat="t in types" value="{{t.id}}">{{t.name}}</option>
                </select>

                <p class="form-control-static" ng-show="banner.banner_id">{{banner.type_name}}</p>

            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="code">Код</label>

            <div class="col-sm-4">
                <input type="text" id="code" ng-model="banner.code" class="form-control" placeholder="Код баннера"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="banner.name" class="form-control"
                       placeholder="Заголовок баннера"/>
            </div>
        </div>

        <span ng-show="banner.banner_id">

            <!-- img | flash -->
            <div class="form-group" ng-show="banner.type_id == 2 || banner.type_id == 3">
                <label class="col-sm-2 control-label" for="file">Загрузка</label>

                <div class="col-sm-10">
                    <input id="file" type="file" ng-file-select="onFileSelect($files,banner)">
                </div>
            </div>

            <!-- img -->
             <div class="form-group" ng-if="banner.type_id == 2 && banner.path">
                 <label class="col-sm-2 control-label" for="image">Изображение</label>

                 <div class="col-sm-10">
                     <img id="image" ng-src="{{banner.path}}" alt="" width="{{getWidth()}}"/>
                 </div>
             </div>

            <!-- flash -->
             <div class="form-group" ng-if="banner.type_id == 3 && banner.path">
                 <label class="col-sm-2 control-label" for="image">Flash</label>

                 <div class="col-sm-10">

                     <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
                             codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0"
                             width="200" height="200">
                         <param name="movie" value="{{banner.path}}"/>
                         <param name="quality" value="high"/>
                         <param name="wmode" value="opaque"/>
                         <param name="bgcolor" value="#ffffff"/>
                         <embed wmode="opaque" src="{{banner.path}}" quality="high" bgcolor="#ffffff" width="{{getWidth()}}"
                                height="{{getHeight()}}" type="application/x-shockwave-flash"
                                pluginspage="http://www.macromedia.com/go/getflashplayer"/>
                     </object>

                 </div>
             </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="width">Width</label>

                <div class="col-sm-4">
                    <input type="text" id="width" ng-model="banner.width" class="form-control"
                           placeholder="Ширина баннера (px)"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="height">Height</label>

                <div class="col-sm-4">
                    <input type="text" id="height" ng-model="banner.height" class="form-control"
                           placeholder="Высота баннера (px)"/>
                </div>
            </div>


            <!-- img && flash -->
            <div class="form-group" ng-show="banner.type_id == 2 || banner.type_id == 3">
                <label class="col-sm-2 control-label" for="url">Ссылка</label>

                <div class="col-sm-10">
                    <div class="input-group">
                        <input type="text" id="name" ng-model="banner.url" class="form-control"
                               placeholder="Ссылка баннера"/>

                        <span class="input-group-btn">
                            <button ng-click="banner.redirect = 1" ng-show="banner.redirect == 0"
                                    class="btn btn-default" type="button">Редирект (Off)
                            </button>
                            <button ng-click="banner.redirect = 0" ng-show="banner.redirect == 1"
                                    class="btn btn-success" type="button">Редирект (On)
                            </button>
                        </span>

                    </div>
                    <!-- /input-group -->
                </div>
            </div>

            <!-- html -->
            <div class="form-group" ng-show="banner.type_id == 1">
                <label class="col-sm-2 control-label" for="text">HTML</label>

                <div class="col-sm-10">
                    <textarea name="text" id="text" ng-model="banner.html" class="form-control"
                              placeholder="HTML код баннера"></textarea>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label" for="pos">Приоритет</label>

                <div class="col-sm-3">
                    <input type="text" id="pos" ng-model="banner.pos" parser-int class="form-control"/>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label" for="date_show_up">Дата показов</label>

                <div class="col-sm-3">
                    <input type="text" id="date_show_up" ng-model="banner.date_show_up" class="form-control"/>

                    <p class="help-block">Если не указано, будет показывать постоянно</p>
                </div>
            </div>


           <fieldset>
               <legend>Статистика</legend>
               <div class="form-group">
                   <label class="col-sm-2 control-label" for="visit">Показы</label>

                   <div class="col-sm-3">
                       <div class="input-group">

                           <input disabled type="text" id="visit" ng-model="banner.visit" class="form-control"
                                  placeholder="Кол-во показов баннера"/>

                            <span class="input-group-btn">
                                <button ng-click="banner.visit = 0" class="btn btn-default" type="button">Обнулить</button>
                            </span>
                       </div>
                   </div>
               </div>

               <!-- img -->
               <div class="form-group" ng-show="banner.type_id == 2 || banner.type_id == 3">
                   <label class="col-sm-2 control-label" for="click">Клики</label>

                   <div class="col-sm-3">
                       <div class="input-group">
                       <input disabled type="text" id="click" ng-model="banner.click" class="form-control"
                              placeholder="Кол-во кликов по баннеру"/>
                           <span class="input-group-btn">
                                <button ng-click="banner.click = 0" class="btn btn-default" type="button">Обнулить</button>
                            </span>
                       </div>
                   </div>
               </div>
           </fieldset>

        </span>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._banner = <?= $banner? \Delorius\Utils\Json::encode((array)$banner): '{status:"0",redirect:0,visit:0,click:0}'?>;
    window._types = <?= $types? \Delorius\Utils\Json::encode((array)$types): '[]'?>;

    $('#date_show_up').datetimepicker({
        lang: 'ru',
        timepicker: true,
        format: 'd.m.Y H:i'
    });
</script>




