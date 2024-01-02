<aside id="callback" class="b-popup b-popup_callback mfp-hide " data-ng-controller="CallbackController">

    <div class="b-popup__layout b-popup__layout_callback">

        <div class="b-callback__h1">У Вас остались вопросы?</div>

        <? if (is_work()): ?>
            <div class="b-callback__h3">Хотите, перезвоним вам за {{timer}} секунд?</div>
        <? else: ?>
            <div class="b-callback__h3">Сейчас мы уже не работаем, но мы можем перезвонить вам завтра.</div>
        <? endif; ?>
        <div class="b-callback__form" data-ng-if="timer_show">
            <input data-ng-model="form.phone" type="text" placeholder="+7 (___) ___-__-__"
                   class="b-callback__input js-phone-mask"/>

            <a class="b-btn b-btn-callback" href="javascript:;" data-ng-click="send('обратный звонок','callback')">
                Жду звонка!
            </a>

            <div class="b-callback__help">Звонок бесплатный</div>
        </div>

        <? if (is_work()): ?>
            <div class="b-callback__timer">
                <div class="b-callback__timer_result">0:<span class="b-callback__callback_time">{{timer_record}}</span>,00
                </div>
            </div>
        <? endif; ?>

    </div>

</aside>


<aside id="letter" class="b-popup b-popup_callback mfp-hide " data-ng-controller="CallbackController">

    <div class="b-popup__header">Написать сообщение</div>

    <div class="b-popup__layout">

        <div class="b-popup__group">
            <div class="b-popup__label">E-mail</div>
            <input data-ng-model="form.email" type="text" class="b-popup__input "/>
        </div>

        <div class="b-popup__group">
            <div class="b-popup__label">Сообщение</div>
            <textarea data-ng-model="form.note" class="b-popup__textarea"></textarea>
        </div>

        <div class="b-popup__group b-popup__group_btn">
            <a class="b-btn b-btn-popup" href="javascript:;" data-ng-click="send_note('Сообщение','note')">Отправить</a>
        </div>

    </div>

</aside>

<aside id="order" class="b-popup b-popup_order mfp-hide " data-ng-controller="CallbackController">

    <div class="b-popup__header">Заказать</div>

    <div class="b-popup__layout">

        <div class="b-popup__group">
            <div class="b-popup__label">Телефон</div>
            <input data-ng-model="form.phone"  placeholder="+7 (___) ___-__-__" type="text" class="b-popup__input js-phone-mask "/>
        </div>

        <div class="b-popup__group b-popup__group_btn">
            <a class="b-btn b-btn-popup" href="javascript:;" data-ng-click="send_order('Заказать','note')">Заказать</a>
        </div>

    </div>

</aside>

<aside id="credit_popup" class="b-popup b-popup_credit mfp-hide ">

    <div id="pos-credit-container"></div>

</aside>

<aside id="credit_popup_form" class="b-popup b-popup_credit_form mfp-hide ">

    <div class="b-form-credit">
        <!--<p>-->
        <!--    <img src="https://www.pochtabank.ru/images/pochtabank/logo.svg"/>-->
        <!--</p>-->

        <button class="btn btn-outline-info btn-block" type="button"
                onclick="open_text('.b-form-credit__modal')">
            Требования для оформления кредита
        </button>


        <div class="b-form-credit__modal hide" tabindex="-1">

            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLongTitle"><strong>Требования к клиенту для
                            оформления кредита:</strong>
                    </h6>
                </div>
                <div class="modal-body">• Гражданство Российской Федерации
                    <br/>
                    • Постоянная регистрация в любом субъекте Российской Федерации
                    <br/>
                    • Наличие собственного мобильного, а также контактного и рабочего номера телефона
                    <br/>
                    • Возраст от 18 лет
                    <br/>
                    <br/>
                    <strong>Необходимые документы:</strong>
                    <br/>
                    • Паспорт гражданина РФ
                    <br/>
                    <strong>Срок действия кредитного решения:</strong>
                    <br/>
                    • 7 дней
                    <br/>
                    <br/>
                    <strong>Сумма заявки:</strong>
                    <br/>
                    • От 3 000 до 300 000 рублей
                    <p>
                        <small>Подписание документов кредитного договора возможно
                            <br/>
                            в <a href="https://www.pochtabank.ru/map">любом отделении Почта Банк</a></small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-info" onclick="open_text('.b-form-credit__modal')" type="button">Закрыть
                    </button>
                </div>
            </div>

        </div>

        <div class="b-form-credit__line alignment">
            <link href="https://onlypb.pochtabank.ru/PBstyles.css" rel=stylesheet type="text/css">

            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script> 
            <script type="text/javascript">
                (function() {
                    emailjs.init("user_1PBEJkILIOeNZOELgKnrJ");
                })();
            </script>
            <script src="https://my.pochtabank.ru/sdk/v2/pos-credit.js"></script>
            <div class="PBnkBox">
                <div class="PBnkHead">
                    <div class="PBnkLogo"></div>
                    <div class="PBnkTitle">Товары для рыбной ловли <br>Заявка в банк</div>
                    <div style="clear:both;"></div>
                </div>
            
                <div class="PBnkForm">
                    <div class="PBnkLine">Укажите</div>
                    <input type="number" class="PBnkInput" required id="chekPrice" placeholder="Стоимость товара" title="От 3 до 300 тысяч рублей"/>
                    <div style="clear:both;"></div>
                </div>
            
                <div class="PBnkForm"><div class="PBnkLine">Срок</div>
                    <select id="termCredit" class="PBnkSelect">
                        <option value="6">Кредит 6 месяцев</option>
                        <option value="12">Кредит 12 месяцев</option>
                        <option value="18">Кредит 18 месяцев</option>
                        <option value="24" selected>Кредит 24 месяца</option>
                        <option value="36">Кредит 36 месяцев</option>
                    </select>
                    <div style="clear:both;"></div>
                </div>
            
                <div class="PBnkForm">
                    <div class="PBnkLine">Укажите</div>
                    <input type="number" class="PBnkInput" required id="firstPayment" placeholder="Первоначальный взнос" title="До 40% от стоимости товара"/>
                    <div style="clear:both;"></div>
                </div>
            
              <button class="PBnkButton" onclick="credit_form()" type="button">Перейти к оформлению заявки</button>
            </div>
            
            <div id="pos-credit-container"></div>
        </div>

    </div>

</aside>


<aside class="catapult-cookie-bar">
    <div class="l-container">
        Мы используем файлы cookie, которые помогают нам создать максимально удобные для посетителя условия пользования
        сайтом.
        <a class="btn" onclick="__ok('popup_cookie')" href="javascript:;">ОК</a>
    </div>
</aside>

<script>
    function uuidv4() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
          return v.toString(16);
        });
    }
       
    function credit_form() {
        var chekPrice = document.querySelector('#chekPrice').value;
        var termCredit = document.querySelector('#termCredit').value;
        var firstPayment = document.querySelector('#firstPayment').value;
        
        var ttName = "ИП Павлинин Владимир Михайлович"; 
        var email = " irina@sportest.ru, kvasovaoa@pochtabank.ru";
        var operId = uuidv4();
        var srok = termCredit;
        var chekprice2 = chekPrice - firstPayment;
        
        var amountCredit = chekPrice - firstPayment;
        var firstPaymentMax = chekPrice * 0.4;
        
        if( amountCredit > 300000 || amountCredit < 3000 || amountCredit == '') {
          alert("Сумма кредита должна быть не менее 3'000 и не более 300'000 рублей");
          return false;
        }
    
        if( firstPayment > firstPaymentMax ) {
          alert("Первый взнос должен быть не более 40% от суммы заявки");
          return false;
        }
        
        var options = {
            operId: operId,
            productCode: 'EXP_MP_PP_+',
            ttCode: '0601001014351',
            toCode: '060100101435',
            ttName: '620062, г. Екатеринбург, ул. Гагарина, д. 10',
            amountCredit: '',
            termCredit: termCredit,  
            firstPayment: Number.parseInt(firstPayment),
            extAppId: '',  
            brokerAgentId: 'NON_BROKER',
            returnUrl: ' https://www.sportest.ru',
            order: [{
                category: '262', 
                mark: 'Товары для рыбной ловли',
                model: 'Товары для рыбной ловли ',
                quantity: 1,
                price: Number.parseInt(chekPrice),
            }]
        };
        
        document.querySelector('.PBnkBox').style.display = 'none';
        
        window.PBSDK.posCreditV2.mount('#pos-credit-container', options);
        document.getElementById('pos-credit-container').scrollIntoView(); 
        window.PBSDK.posCreditV2.on('done', function(id) {
          var templateParams = {
            e_site: ttName,
            e_id: id,
            e_summ: Number.parseInt(chekprice2),
            e_srok: srok,
            e_operid: operId,
            e_mail: email,
          };
          emailjs.send('service_f147e9c', 'template_e13tj4p', templateParams);
        });
    }
    
    function __ok(name) {
        df.setCookie(name, true);
        $('.catapult-cookie-bar').removeClass('has-cookie-bar');
    }

    $(function () {
        if (!df.getCookie('popup_cookie')) {
            $('.catapult-cookie-bar').addClass('has-cookie-bar');
        }
    });
</script>
<style>
    .pb-sdk-pos-credit .cont---DMt8s {
        height: auto;
    }
    .alignment {
        justify-content: center;
        align-items: center;
    }
    .b-form-credit {
        overflow: hidden;
    }
    .b-popup .mfp-close {
        color: #000000 !important;
        border-color: #000000 !important;
        padding: 0 !important;
        margin: 0 !important;
        justify-content: center;
        align-items: center;
        display: flex;
    }
    .b-form-credit {
        margin-top: 40px;
    }
</style>