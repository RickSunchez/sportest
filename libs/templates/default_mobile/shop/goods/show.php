<? $attrs = $goods->getAttributes(); ?>
<article class="b-product b-page__content b-goods / item hproduct /" data-id="<?= $goods->pk(); ?>"
         id="product_<?= $goods->pk(); ?>">
    <div class="b-page-show__text">

        <div class="b-product__image js-product-carousel">
            <? if (count($images)): ?>
                <? foreach ($images as $image): ?>
                    <div class="item">
                        <img src="<?= $image->preview ?>" alt="<?= $image->name ?>">
                    </div>
                <? endforeach; ?>
            <? endif; ?>

        </div>
        <h1 class="b-page__title b-product__name"><?= $goods->name; ?></h1>

        <div class="b-product__price">
           <span class="name">
                Цена:
            </span>
            <span class="price init-price">
                 <?= $goods->getPrice() ?>
            </span>
        </div>

        <?= $this->action('Shop:Commodity:Option:list', array('goods_id' => $goods->pk())); ?>



        <a class="m-btn b-btn__order <?= $basket->getQuantity($goods->combination_hash) != 0 ? 'b-btn__order--inner' : '' ?>"
           onclick="add_cart(<?= $goods->pk() ?>)"
           href="javascript:;"></a>


        <div id="sections" class="b-sections">
            <? if ($chars = $goods->getGroupCharacteristics()): ?>
                <section class="b-sections__item">
                    <h2 class="b-sections__name">Характеристики:</h2>
                    <section class="b-sections__text">
                        <ul class="b-characteristics">
                            <? foreach ($chars as $char): ?>
                                <? foreach ($char as $characteristic): ?>
                                    <li class="identifier">
                                        <span class="type"><?= $characteristic['chara']['name'] ?>:</span>
                                        <span class="value">
                                             <? if (count($characteristic['values'])): ?>
                                                 <? foreach ($characteristic['values'] as $key => $value): ?>
                                                     <?= $key == 0 ? '' : ',' ?>
                                                     <?= $value['name'] ?> <?= $value['unit'] ?>
                                                 <? endforeach; ?>
                                             <? else: ?>
                                                 <?= $characteristic['value']['name'] ?> <?= $characteristic['value']['unit'] ?>
                                             <? endif; ?>

                                        </span>
                                    </li>
                                <? endforeach ?>
                            <? endforeach ?>
                        </ul>
                    </section>
                </section>
            <? endif ?>

            <? if (count($sections)): ?>
                <? foreach ($sections as $key => $section): ?>

                    <? if ($section->text): ?>
                        <section class="b-sections__item">
                            <h2 class="b-sections__name "><?= $section->name; ?></h2>
                            <section
                                class="b-sections__text b-text <?= $key == 0 ? ' / description /" itemprop="description' : '' ?>">

                                <?= $section->text; ?>
                            </section>
                        </section>
                    <? endif ?>
                <? endforeach ?>

            <? endif; ?>


        </div>


        <footer class="m-goods__footer">

            <?= $this->action('Shop:Commodity:Goods:accompanies', array(
                'goodsId' => $goods->pk(),
                'type_id' => \Shop\Commodity\Entity\Accompany::TYPE_ADDITIONS,
                'theme' => 'additions'
            )); ?>


            <?= $this->action('Shop:Commodity:Goods:inRnd', array(
                'goodsId' => $goods->pk(),
                'catId' => $goods->cid,
                'limit' => 6
            )); ?>

        </footer>

    </div>
</article>

<style>
    .breadcrumb li.active {
        display: none;
    }
</style>

<script type="text/javascript">
    $(function () {
        $('.b-sections__name').addClass('b-sections__name--close');
    });
</script>