<div data-ng-controller="ReviewController">
    <h1>Добавить отзыв</h1>

    <form role="form" class="well" >
        <div class="form-group">
            <label for="name">Ваше имя и фамилия:*</label>
            <input ng-model="form.name" type="text" class="form-control" id="name" placeholder="">
        </div>
        <div class="form-group">
            <label for="phone">Телефон:*</label>
            <input ng-model="form.phone" type="text" class="form-control" id="phone" placeholder="">
        </div>
        <div class="form-group">
            <label for="email">Электронная почта:*</label>
            <input ng-model="form.email" type="text" class="form-control" id="email" placeholder="">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Ваш отзыв</label>
            <textarea ng-model="form.text" class="form-control" rows="4"  ></textarea>
        </div>
        <button type="button" class="btn btn-success" ng-click="send()" >Отправить</button>
    </form>
</div>