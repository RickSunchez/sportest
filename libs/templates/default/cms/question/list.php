<article class="b-page__show">

    <header class="b-page__header b-page__header_upper">
        <h1>Вопрос-ответ</h1>
        <a class="b-btn b-btn_to_ask" href="#ask">Задать вопрос</a>
    </header>

    <section class="b-page__content">

        <? if (count($questions)): ?>
            <? foreach ($questions as $question): ?>
                <section class="b-ask-item">


                    <div class="b-ask-item__text">
                        <time><?= date('d/m/Y', $question->date_cr) ?>12/10/2015</time>
                        <div class="b-ask-item__name"><strong><?= $question->name ?></strong></div>
                        <div class="b-ask-item__comment">
                            <?= $question->text ?>
                        </div>

                    </div>
                    <? if ($question->answer): ?>
                        <div class="b-ask-item__answer">
                            <span class="crop"></span>
                            <time><?= date('d/m/Y', $question->date_edit) ?></time>
                            <div class="b-ask-item__answer-label"><strong>Ответ:</strong></div>
                            <div class="b-ask-item__comment">
                                <?= $question->answer ?>
                            </div>
                        </div>
                    <? endif; ?>

                </section>
                <!-- .b-ask-item -->
            <? endforeach; ?>
        <? else: ?>
            <p>В наднынй момент нет вопросов, но вы можете задать свой вопрос</p>
        <? endif; ?>


    </section>
    <!-- .b-page__content  -->

    <footer class="b-page__pagination">
        <?= $pagination; ?>
    </footer>

    <aside class="b-form-ask" id="ask" data-ng-controller="QuestionController">
        <h1>Задать вопрос:</h1>

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
                    <input type="text" data-ng-model="form.contact"/>
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