<? if( count($items) > 0 ): ?>
    <section class="comment">
        <!-- comment-preview -->
        <? foreach( $items as $item ): ?>
            <?= $this->partial('shop/goods/review/item', array('item' => $item, 'user' => $users[$item->user_id]))?>
        <? endforeach ?>
        <!-- end comment-preview -->
        <!-- comment-preview -->
        <?if($pagination->getItemsPerPage() < $pagination->getItemCount()):?>
            <div class="bottom-button-line">
                <a href="#" class="more-reviews" id="goods-<?=$item->goods_id?>">Показать еще <span id="more-reviews-count"><?=$pagination->getNextCountItems()?></span> из <span id="more-reviews-from"><?=$pagination->getRemainItems()?></span> отзывов</a>
            </div>
        <?endif?>
    </section>
<? endif ?>