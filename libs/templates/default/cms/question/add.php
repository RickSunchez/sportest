<article class="b-page__show">

    <header class="b-page__header b-page__header_upper">
        <h1>Задать вопрос</h1>
        <a class="b-btn b-btn_to_ask" href="#ask">Задать вопрос</a>
    </header>

    <aside class="b-form-ask" id="ask" data-ng-controller="ReviewCtrl">

        <div class="b-table b-form-ask__layout">
            <div class="b-table-row">
                <div class="b-table-cell b-form-ask__label">Ваше имя:</div>
                <div class="b-table-cell b-form-ask__input">
                    <input type="text" data-ng-model="form.name"/>
                </div>
            </div>
            <div class="b-table-row">
                <div class="b-table-cell b-form-ask__label">Контакт:</div>
                <div class="b-table-cell b-form-ask__input">
                    <input type="text" data-ng-model="form.email"/>
                </div>
            </div>
            <div class="b-table-row">
                <div class="b-table-cell b-form-ask__label b-form-ask__label_textarea">Ваш вопрос:</div>
                <div class="b-table-cell b-form-ask__textarea">
                    <textarea data-ng-model="form.text"></textarea>
                </div>
            </div>
            <div class="b-table-row">
                <div class="b-table-cell b-form-ask__label"></div>
                <div class="b-table-cell b-form-ask__btn">
                    <a class="b-btn b-btn_ask" href="javascript:;" data-ng-click="send()">Задать вопрос</a>
                </div>
            </div>
        </div>

    </aside>
</article>