<div class="b-form" data-ng-controller="CallbackController">

    <div class="b-form-callback__name">Форма заказа запчастей</div>

    <div class="b-form-callback__group">
        <div class="b-form-callback__label">
            Ваше Имя:
        </div>
        <input data-ng-model="form.name" class="b-form-callback__input">
    </div>

    <div class="b-form-callback__group">
        <div class="b-form-callback__label">
            Телефон:
        </div>
        <input data-ng-model="form.phone" class="b-form-callback__input js-phone-mask" placeholder="+7 (___) ___-__-__">
    </div>
    <div class="b-form-callback__group">
        <div class="b-form-callback__label">
            Ремонтируемый объект:
        </div>
        <input data-ng-model="form.object" class="b-form-callback__input " placeholder="мотор/лодка/мотобуксировщик/...">
    </div>
    <div class="b-form-callback__group">
        <div class="b-form-callback__label">
            Запчасть:
        </div>
        <input data-ng-model="form.part" class="b-form-callback__input " placeholder="Артикул/Название/Где расположена">
    </div>
    <div class="b-form-callback__group">
        <div class="b-form-callback__label">
            Комментарий к запросу:
        </div>
        <textarea class="b-form-callback__textarea" data-ng-model="form.note"
                  placeholder="Замечания или вопросы по запросу"></textarea>
    </div>

    <div class="b-form-callback__group">
        <a class="m-btn m-btn_callback " data-ng-click="send_part('Заказ запчасти')" href="javascript:;">Отправить</a>
    </div>
</div>