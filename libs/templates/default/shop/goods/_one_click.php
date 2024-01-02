<section class="b-popup b-popup_cart" data-ng-controller="OneClickController" data-ng-init='init(<?= \Delorius\Utils\Json::encode($product_data)?>)' >
    <h1 class="b-popup__title">Быстрый заказ</h1>
    <div class="b-popup__layout">


        <div class="b-popup_cart__form">
            <div class="b-popup_cart__form__info">
                Пожалуйста, заполните контактную информацию. <br />
                Сотрудники службы заказа свяжутся с вами в рабочее время.
            </div>

            <div class="b-table">
                <div class="b-table-row">
                    <div class="b-table-cell b-popup_cart__form__label">Ваше имя:</div>
                    <div class="b-table-cell b-popup_cart__form__input"><input type="text" data-ng-model="form.name" /></div>
                </div>
                <div class="b-table-row">
                    <div class="b-table-cell b-popup_cart__form__label">Мобильный телефон:</div>
                    <div class="b-table-cell b-popup_cart__form__input"><input class="js-phone-mask" type="text" data-ng-model="form.phone" placeholder="+7 (___) ___-__-__" /></div>
                </div>
                <div class="b-table-row">
                    <div class="b-table-cell b-popup_cart__form__label">Электронная почта:</div>
                    <div class="b-table-cell b-popup_cart__form__input"><input type="text" data-ng-model="form.email" /></div>
                </div>
                <div class="b-table-row">
                    <div class="b-table-cell b-popup_cart__form__label">Комментарий:</div>
                    <div class="b-table-cell b-popup_cart__form__textarea"><textarea data-ng-model="form.note" ></textarea></div>
                </div>
            </div>

        </div><!-- .b-popup_cart__form -->

        <div class="b-table b-popup_cart__item">
            <div class="b-table-cell b-popup_cart__image">
                <? if ($goods->image): ?>
                    <img alt="<?= $goods->image->name ?>" src="<?= $goods->image->preview ?>"/>
                <? else: ?>
                    <img alt="<?= $goods->name ?>" src="/source/images/no.png"/>
                <? endif; ?>
            </div>
            <div class="b-table-cell b-popup_cart__info">
                <h1><?= $goods->name ?></h1>
                <?if(count($goods->options)):?>
                    <?foreach($goods->options as $value):?>
                        <div class="b-popup_cart__options">
                            <b><?= $value['option']['name']?></b>:  <?= $value['variant']['name']?>
                        </div>
                        <?endforeach?>
                <?endif;?>
            </div>
            <div class="b-table-cell b-popup_cart__price">
                <span>цена:</span> <?= $goods->getPrice(); ?>
            </div>
        </div>


        <? if (count($additions)): ?>
            <section class="b-popup_cart__addition">
                <h2>Дополнения</h2>

                <? foreach ($additions as $addition): ?>
                    <div class="b-table b-popup_cart__item">
                        <div class="b-table-cell b-popup_cart__image">
                            <? $image = $addition->image ? $addition->image : $addition->getMainImage() ?>
                            <? if ($image->loaded()): ?>
                                <img alt="<?= $image->name ?>" src="<?= $image->preview ?>"/>
                            <? else: ?>
                                <img alt="<?= $addition->name ?>" src="/source/images/no.png"/>
                            <? endif; ?>
                        </div>
                        <div class="b-table-cell b-popup_cart__info">
                            <h1><?= $addition->name ?></h1>
                        </div>
                        <div class="b-table-cell b-popup_cart__price">
                            <span>цена:</span> <?= $addition->getPrice(); ?>
                        </div>
                    </div>
                <? endforeach; ?>
            </section>

        <? endif; ?>


        <div class="b-table b-popup_cart__btn">
            <div class="b-table-cell"></div>
            <div class="b-table-cell">
                <a class="b-btn b-btn__in-cart" href="javascript:;" data-ng-click="send();">Отправить</a>
            </div>
        </div>
    </div>

</section>

