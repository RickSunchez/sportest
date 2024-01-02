<div class="b-review__form"  ng-controller="GoodsReviewController" ng-init="init('<?=$goods->pk()?>')">
    <div class="title">Оставьте отзыв</div>

    <form action="#" name="#" role="form">
        <div class="info-text">
            Заполните обязательные поля <span class="red-text">*</span>
        </div>
        <div class="line">
            <label>Положительные качества: <span class="red-text">*</span></label>
            <input class="text-input" type="text" ng-model="form.plus" />
        </div>
        <div class="line">
            <label>Отрицательные качества: <span class="red-text">*</span></label>
            <input class="text-input" type="text" ng-model="form.minus" />
        </div>
        <div class="line">
            <label>Комментарий <span class="red-text">*</span></label>
            <textarea ng-model="form.text"></textarea>
        </div>
        <div class="rating">
            <div class="rating-title">Оценка <span class="red-text">*</span></div>
        </div>

        <div class="button-line">
            <button class="green-button" name="#" ng-click="send()">Отправить</button>
        </div>
    </form>
</div>
