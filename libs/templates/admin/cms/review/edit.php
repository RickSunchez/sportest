<div ng-controller="ReviewController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_review', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Отзыв</h1>

    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

            <div class="col-sm-10">
                <p class="form-control-static">
                    <input type="checkbox" ng-model="review.status" ng-true-value="'1'" id="inputstatus"
                           ng-false-value="'0'"/> Показать отзыв</p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="date_alarm">Дата создания</label>

            <div class="col-sm-3">
                <input type="text" id="date_alarm" ng-model="review.date_cr" class="form-control"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="answer">Ретинг</label>

            <div class="col-sm-10">
                <select ng-model="review.rating" class="form-control">
                    <option value="0">Нет</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Автор</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="review.author" class="form-control"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="location">Место проживания</label>

            <div class="col-sm-10">
                <input type="text" id="location" ng-model="review.location" class="form-control"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="callback">Обратная связь</label>

            <div class="col-sm-10">
                <input type="text" id="callback" ng-model="review.callback" class="form-control"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Отзыв</label>

            <div class="col-sm-10">
                <textarea name="text" id="text" ng-model="review.text" style="height: 100px;"
                          class="form-control"></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="date_answer">Дата ответа</label>

            <div class="col-sm-3">
                <input type="text" id="date_answer" ng-model="review.date_answer" class="form-control"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="answer">Ответ на отзыв</label>

            <div class="col-sm-10">
                <textarea name="text" id="answer" ng-model="review.answer" style="height: 100px;"
                          class="form-control"></textarea>
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
    window._review = <?= $review ? \Delorius\Utils\Json::encode((array)$review) : '{status:"0",rating:"0"}'?>;
    window._image = <?= $image ? \Delorius\Utils\Json::encode((array)$image) : 'null'?>;

    $('#date_alarm, #date_answer').datetimepicker({
        lang: 'ru',
        timepicker: true,
        format: 'd.m.Y H:i'
    });
</script>




