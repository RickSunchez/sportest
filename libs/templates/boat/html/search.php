<aside class="b-search" role="search">
    <div class="l-container">

        <a href="javascript:;" class="b-category-btn">
            <img class="open" src="/source/images/boat/icon_menu.png" alt="Open">
            <img class="close" src="/source/images/boat/icon_close.png" alt="Close">
            Каталог товаров
        </a>
        <form class="b-search__form" action="<?= link_to('goods_search') ?>">
            <input  autocomplete="off"
                    autofocus
                    name="query"
                    class="b-search__input"
                    placeholder="Что нужно найти?"/>
            <button type="submit" class="b-search__btn"></button>
        </form>

    </div>
    <div class="l-container ">
<!--        --><?//= $this->action('Location:Store:Shop:multiMenu')?>
        <?= $this->action('Boat:Store:Html:menuTop')?>
    </div>
</aside>






