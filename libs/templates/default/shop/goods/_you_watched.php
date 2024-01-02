<h2>Вы смотрели</h2>
<ul class="popular-products clearfix">
    <li class="clearfix">
        <a class="photo" href="<?= link_to('shop_goods',array('id'=>$goods['goods_id'],'url'=>$goods['url']))?>">
            <?if(sizeof($image)):?>
                <img width="62" height="62" src="<?= $image['preview']?>" alt=""/>
            <?else:?>
                <img width="62" height="62" src="/source/images/no.png" alt=""/>
            <?endif;?>
        </a>

<!--        <p>только что</p>-->
        <h4>
            <a href="<?= link_to('shop_goods',array('id'=>$goods['goods_id'],'url'=>$goods['url']))?>">
                <?= $goods['name']?>
            </a>
        </h4>

        <p class="article">Артикул: <?= $goods['article']?></p>

        <p class="price"><?= (int)$goods['value']?> р.</p>
    </li>
</ul>