<div class="b-basket" data-ng-controller="CartMiniController" data-ng-init="init()"
     data-ng-class="{'b-basket--active':count!=0}">
    <span class="b-basket__count">{{count}}</span>
    <input type="hidden" data-ng-click="init()" class="js-cart-update">
    <a title="Перейти в карзину" data-href="<?= link_to('shop_cart') ?>"></a>
</div>