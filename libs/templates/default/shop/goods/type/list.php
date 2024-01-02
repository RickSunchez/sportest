<div class="wrap clearfix">

    <div class="filter-main">

        <?= $pagination->render(); ?>

        <ul class="filter-products-nav clearfix">
            <?foreach($goods as $item):?>
                <li class="clearfix">
                    <a class="photo" href="<?= link_to('shop_goods',array('id'=>$item->pk(),'url'=>$item->url))?>">
                        <? if (isset($images[$item->pk()])): ?>
                            <img width="55" height="55" src="<?= $images[$item->pk()]->preview; ?>" alt=""/>
                        <? else: ?>
                            <img width="55" height="55" src="/source/images/no.png" alt=""/>
                        <?endif ?>
                    </a>

                    <div class="info">
                        <h4><a href="<?= link_to('shop_goods',array('id'=>$item->pk(),'url'=>$item->url))?>"><?= $item->name?></a></h4>

                        <?if($item->article):?><p class="article">Арт. <?= $item->article?></p><?endif;?>
                    </div>
                    <a href="javascript:{}" onclick="AddBasketGoods(<?= $item->pk()?>);" class="cart btn_in_cart ">В Корзину</a>

                    <p class="price">цена: <em><?= (int)$item->value?></em> <span>руб.</span></p>
                </li>
            <?endforeach;?>
        </ul>

        <?= $pagination->render(); ?>

    </div>
    <div class="aside-right">

        <?= $this->action('Shop:Commodity:Goods:youWatched');?>
    </div>
</div><!-- end .wrap -->

<?= $this->partial('html/before_buy',array('class'=>'action-block')); ?>