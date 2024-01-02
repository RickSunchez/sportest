<?php

namespace Boat\Store\Component\Cart\Payment;

use Shop\Store\Component\Cart\Payment\PaymentBase;

class PaymentPochtaBank extends PaymentBase
{

    public function render()
    {
        $valueGoods = $this->cart->getValueGoods();
        $goods = $this->cart->getProducts();
        $items = '';
        foreach ($goods as $item) {
            $items .= _sf('{ mark:"{0}",model:"{0}", quantity:{1}, price:{2} },',
                $item->name,
                $item->amount,
                $item->getPrice(false, false)
            );
        }

        $orders = _sf('[{0}]', $items);
        return '
        <div class="b-form-credit" style="border: 1px solid #dddddd; margin-bottom: 10px;">
    
            <button class="btn btn-outline-info btn-block" type="button"
                    onclick=\'open_text(".b-form-credit__modal")\'>
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
                        <button class="btn btn-outline-info" onclick=\'open_text(".b-form-credit__modal")\' type="button">Закрыть
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
    
        <script>
            function uuidv4() {
                return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(c) {
                  var r = Math.random() * 16 | 0, v = c == "x" ? r : (r & 0x3 | 0x8);
                  return v.toString(16);
                });
            }
               
            function credit_form() {
                var chekPrice = document.querySelector("#chekPrice").value;
                var termCredit = document.querySelector("#termCredit").value;
                var firstPayment = document.querySelector("#firstPayment").value;
                
                var ttName = "ИП Павлинин Владимир Михайлович"; 
                var email = " irina@sportest.ru, kvasovaoa@pochtabank.ru";
                var operId = uuidv4();
                var srok = termCredit;
                var chekprice2 = chekPrice - firstPayment;
                
                var amountCredit = chekPrice - firstPayment;
                var firstPaymentMax = chekPrice * 0.4;
                
                if( amountCredit > 300000 || amountCredit < 3000 || amountCredit == "") {
                  alert("Сумма кредита должна быть не менее 3000 и не более 300000 рублей");
                  return false;
                }
            
                if( firstPayment > firstPaymentMax ) {
                  alert("Первый взнос должен быть не более 40% от суммы заявки");
                  return false;
                }
                
                var options = {
                    operId: operId,
                    productCode: "EXP_MP_PP_+",
                    ttCode: "0601001014351",
                    toCode: "060100101435",
                    ttName: "620062, г. Екатеринбург, ул. Гагарина, д. 10",
                    /*amountCredit: ' . $valueGoods . ',*/
                    termCredit: termCredit,  
                    firstPayment: Number.parseInt(firstPayment),
                    extAppId: "",  
                    brokerAgentId: "NON_BROKER",
                    returnUrl: "https://www.sportest.ru",
                    /*order:' . $orders . '*/
                };
                
                document.querySelector(".PBnkBox").style.display = "none";
                
                window.PBSDK.posCreditV2.mount("#pos-credit-container", options);
                document.getElementById("pos-credit-container").scrollIntoView(); 
                window.PBSDK.posCreditV2.on("done", function(id) {
                  var templateParams = {
                    e_site: ttName,
                    e_id: id,
                    e_summ: Number.parseInt(chekprice2),
                    e_srok: srok,
                    e_operid: operId,
                    e_mail: email,
                  };
                  emailjs.send("service_f147e9c", "template_e13tj4p", templateParams);
                });
            }
        </script>
           
        ';
    }

}