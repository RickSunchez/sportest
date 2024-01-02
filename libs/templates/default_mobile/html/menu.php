<div class="m-menu <?= $fixed ? 'm-menu_fixed' : '' ?>" id="top">

    <div class="b-table">

        <div class="b-table-cell m-menu__btn-space">
            <div class="m-menu__button m-layout__toggle-menu">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </div>
        </div>
        <div class="b-table-cell m-menu__logo">
            <div class="m-logo" data-href="<?= homepage(); ?>">
                <img src="/source/images/banya/logo.png" alt="Компания БаняПросто"></div>
        </div>
        <div class="b-table-cell m-menu__search-btn">
            <span data-open="search" class="btn-search"></span>
        </div>
        <div class="b-table-cell m-menu__cart">
            <?= $this->action('Shop:Store:Cart:cartMini', array('list' => false)) ?>
        </div>
    </div>
</div>


<aside data-model="search" class="b-search-model" data-ng-controller="SearchController">
    <div class="b-model__header">
        <button class="b-model__close js-model--close"></button>
        Поиск
    </div>
    <form class="m-menu-search">
        <input data-ng-change="search()" data-ng-model="term"
               class="m-menu-search__input"
               name="query"
               type="text" placeholder="Поиск по магазину">
        <button data-ng-click="send()" type="button" class="m-menu-search__btn">Поиск</button>
    </form>
    <div class="b-model__layout">
        <div class="b-search-item" data-ng-repeat="item in items" data-ng-click="link(item.link)">
            <span data-ng-bind-html="getName(item.name)"></span>
        </div>
    </div>

</aside>

