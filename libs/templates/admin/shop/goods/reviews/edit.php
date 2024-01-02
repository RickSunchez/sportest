<div ng-controller="GoodsReviewController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_reviews', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Отзыв к товару</h1>

    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="desc">
                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="review.status" ng-true-value="1" id="inputstatus"
                                   ng-false-value="0"/> Показать отзыв</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Автор</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <a title="Редактировать"
                               href="<?= link_to('admin_user', array('action' => 'edit','id'=>$user->pk())) ?>">
                                <?=$user->email?> <i class="glyphicon glyphicon-share"></i>
                            </a>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="date_alarm">Дата создания</label>

                    <div class="col-sm-3">
                        <p class="form-control-static">
                        {{review.created}}
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">Товар</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <a href="<?= link_to('admin_goods', array('action' => 'edit','id'=>$goods->pk())) ?>">
                                <?=$goods->name?>
                                <i class="glyphicon glyphicon-share"></i>
                            </a>
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="preview">Плюсы:</label>

                    <div class="col-sm-10">
                        <input name="plus" ng-model="review.plus" class="form-control" placeholder="" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="preview">Минусы:</label>

                    <div class="col-sm-10">
                        <input name="minus" ng-model="review.minus" class="form-control" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="preview">Комментарий:</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="preview" ng-model="review.text" class="form-control" placeholder=""
                                  style="height: 150px;"></textarea>
                    </div>
                </div>

                <div class="form-group required">
                    <label class="col-sm-2 control-label" for="inputdecimal_type">Оценка</label>
                    <div class="col-sm-5">
                        <select  class="form-control" ng-model="review.rating" >
                            <option value="0">Выберите</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- #deac -->

            <div class="tab-pane" id="meta">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">Заголовок страницы</label>

                    <div class="col-sm-10">
                        <input type="text" id="title" ng-model="meta.title" class="form-control"
                               placeholder="Заголовок страницы"/>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="keys">Ключевые слова</label>

                    <div class="col-sm-10">
                        <textarea id="keys" ng-model="meta.keys" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="desc">Краткое описания страницы</label>

                    <div class="col-sm-10">
                        <textarea id="desc" ng-model="meta.desc" class="form-control" onblur=""></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="redirect">Редирект</label>

                    <div class="col-sm-10">
                        <input type="text" id="redirect" ng-model="meta.redirect" class="form-control"
                               placeholder="Адрес ссылки"/>
                        <span class="help-block">Если необходиво перенаправить пользователя при переходе</span>
                    </div>
                </div>

            </div>
            <!-- #meta -->
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._review = <?= $review ? \Delorius\Utils\Json::encode((array)$review): '{}'?>;
</script>




