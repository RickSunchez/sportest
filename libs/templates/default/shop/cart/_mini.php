<div data-ng-controller="CartMiniController" data-ng-init="count=<?= (int)$count ?>">
    <div data-href="<?= link_to('shop_cart') ?>" title="{{getTitle()}}" data-ng-cloak class="b-cart-min"
         data-ng-class="{'b-cart-min_show':count!=0}">
        <img class="b-cart-min__icon" src="/source/images/mobile/cart.png" alt="Корзина">

        <div class="b-cart-min__info">
            В корзине:
            <b data-ng-bind="count | ifEmpty:'<?= $count ?>' "><?= $count ?></b>
            <b data-ng-bind="count_prefix | ifEmpty:'<?= $count_prefix ?>' "><?= $count_prefix ?></b>
            <br/>
            На сумму:
            <b class="price"
               data-ng-bind-html="price | to_html | ifEmptySelect:'.b-cart-min__info .price'"
                ><?= $price ?></b>
        </div>
    </div>
    <input class="js-cart-update" type="hidden" data-ng-click="init(false)">
</div>