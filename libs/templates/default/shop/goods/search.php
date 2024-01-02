<? if (empty($query)): ?>
    <h2>Строка для поиска должна быть не менее 4 символов</h2>
<? else: ?>
    <h2>Вы ищите: "<?= $query ?>"</h2>
    <? if ($pagination->getItemCount()): ?>
        <div class="b-goods_list">
            <? foreach ($goods as $item): ?>
                <div id="goods_<?= $item->pk() ?>" class="item">
                    <div class="img">
                        <? if ($item->image): ?>
                        <a title="<?= $item->name ?>" class="img_hover" href="<?= $item->image->normal;?>"> <img alt="<?= $item->name ?>" width="120" height="120"
                                 src="<?= $item->image->preview ?>"/>
                            </a>
                        <? else: ?>
                            <img alt="<?= $item->name ?>" width="120" height="120" src="/source/images/zero.gif"/>
                        <?endif; ?>
                    </div>
                    <div class="block">
                        <div class="text">
                            <h2><?= $item->name ?></h2>

                            <div class="brief"><?= $item->brief ?></div>
                        </div>
                        <div class="header_table">
                            <div class="stock">В наличии</div>
                            <div class="art">Арт.</div>
                            <div class="count">Кол-во</div>
                            <div class="price">
                                Цена/<?= (isset($unit[$item->unit_id])) ? $unit[$item->unit_id]->abbr : 'шт.' ?></div>
                            <div class="add">
                                <a onclick="return AddBasketGoods(<?= $item->pk(); ?>)"
                                   href="#"
                                   class="<?= $basket->getQuantity($item->pk()) ? 'active' : '' ?>"
                                    >
                                    <span class="new">+Добавить в заказ</span>
                                    <span class="adder">Добавлено в заказ</span>
                                </a>
                            </div>
                        </div>
                        <div class="body_table">
                            <div class="stock">есть</div>
                            <div class="art"><?= $item->article; ?></div>
                            <div class="count">
                                <div class="select_count">
                                    <a onclick="return MinusQuantity(<?= $item->pk(); ?>)" class="minus" href="#">-</a>
                                    <input class="quantity" type="text" value="1"/>
                                    <a onclick="return PlusQuantity(<?= $item->pk(); ?>)" class="plus" href="#">+</a>
                                </div>
                                <div class="abbr">
                                    <? if (isset($unit[$item->unit_id])): ?>
                                        <?= $unit[$item->unit_id]->abbr; ?>
                                    <? endif ?>
                                </div>
                            </div>
                            <div class="price">
                                УТОЧНЯТЬ У МЕНЕДЖЕРА
                            </div>
                        </div>
                    </div>
                </div>

            <? endforeach; ?>
            <?= $pagination->render(); ?>
        </div>
    <? else: ?>
        <p>К сожалению, по вашему запросу ничего не найдено.</p>
        <p>Попробуйте поискать по каталогу.</p>
    <?endif; ?>
<?endif; ?>