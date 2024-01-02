<? if (count($categories[0])): ?>

    <div class="b-menu-horiz__layout">

        <div class="b-menu-horiz__label">
            Каталог товаров
        </div>
        <ul class="b-menu-horiz__category">
            <? foreach ($categories[0] as $cat): ?>
                <li class="b-menu-horiz__category-item">
                    <a class="b-menu-horiz__link"
                       href="<?= link_to('shop_category_list', array('cid' => $cat['id'], 'url' => $cat['url'])) ?>">
                        <?= $cat['name'] ?>
                        <div class="b-menu-horiz__link-corner"></div>
                    </a>

                    <? if (count($categories[$cat['id']])): ?>
                        <ul class="b-menu-horiz__sub-category">
                            <? foreach ($categories[$cat['id']] as $cat): ?>
                                <li data-href="<?= link_to('shop_category_list', array('cid' => $cat['id'], 'url' => $cat['url'])) ?>">
                                    <a class="b-link b-menu-horiz__link"
                                       href="<?= link_to('shop_category_list', array('cid' => $cat['id'], 'url' => $cat['url'])) ?>">
                                        <?= $cat['name'] ?>
                                    </a>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    <? endif; ?>
                </li>
            <? endforeach ?>
        </ul>

    </div>

<? endif; ?>


<script>
    $(function () {
        $('.b-menu-horiz__link').click(function (e) {
            e.preventDefault();
            var $li = $(this).parent('li');
            if ($li.hasClass('active')) {
                $li.find("ul").slideUp(300, function() {
                    $li.removeClass("active");
                })
            } else {
                $li.addClass('active');
                $li.find("ul").slideDown(300, function () {});
            }
        });
    })
</script>


