<div class="b-form" data-ng-controller="CallbackController">

    <div class="b-form__name">Форма заказа запчастей</div>

    <div class="b-form__group">
        <div class="b-form__label">
            Ваше Имя:
        </div>
        <input data-ng-model="form.name" class="b-form__input">
    </div>

    <div class="b-form__group">
        <div class="b-form__label">
            Телефон:
        </div>
        <input data-ng-model="form.phone" class="b-form__input js-phone-mask" placeholder="+7 (___) ___-__-__">
    </div>
    <div class="b-form__group">
        <div class="b-form__label">
            Ремонтируемый объект:
        </div>
        <input data-ng-model="form.object" class="b-form__input " placeholder="мотор/лодка/мотобуксировщик/...">
    </div>
    <div class="b-form__group">
        <div class="b-form__label">
            Запчасть:
        </div>
        <input data-ng-model="form.part" class="b-form__input " placeholder="Артикул/Название/Где расположена">
    </div>
    <div class="b-form__group">
        <div class="b-form__label">
            Комментарий к запросу:
        </div>
        <textarea class="b-form__textarea" data-ng-model="form.note"
                  placeholder="Замечания или вопросы по запросу"></textarea>
    </div>

    <div class="b-form__group">
        <a class="b-btn" data-ng-click="send_part('Заказ запчасти','order_details')" href="javascript:;">Отправить</a>
    </div>
</div>