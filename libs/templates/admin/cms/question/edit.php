<div ng-controller="QuestionController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_question', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Вопрос</h1>

    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

            <div class="col-sm-10">
                <p class="form-control-static">
                    <input type="checkbox" ng-model="question.status" ng-true-value="'1'" id="inputstatus"
                           ng-false-value="'0'"/> Показать</p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Имя</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="question.name" class="form-control"/>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="contact">Контакт</label>

            <div class="col-sm-10">
                <input type="text" id="contact" ng-model="question.contact" class="form-control"/>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="phone">Телефон</label>

            <div class="col-sm-10">
                <input type="text" id="phone" ng-model="question.phone" class="form-control"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="email">E-mail</label>

            <div class="col-sm-10">
                <input type="text" id="email" ng-model="question.email" class="form-control"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Вопрос</label>

            <div class="col-sm-10">
                <textarea name="text" id="text" ng-model="question.text" class="form-control"></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="answer">Ответ</label>

            <div class="col-sm-10">
                <textarea name="text" id="answer" ng-model="question.answer" class="form-control"></textarea>
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
    window._question = <?= $question? \Delorius\Utils\Json::encode((array)$question): '{status:"0"}'?>;
</script>




