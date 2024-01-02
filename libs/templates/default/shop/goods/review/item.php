<article class="comment-preview">
    <div class="dark-line clearfix">
        <div class="name left"><?=$user->login?></div>

        <div class="rating left" data-score="<?=$item->rating?>"></div>

        <div class="date right">
            <?=date('d.m.Y', $item->date_cr)?>
        </div>
    </div>

    <div class="text">
        <? if(strlen($item->plus) > 0): ?>
            <p><span class="comment-title">Достоинства:</span> <?=\CMS\Core\Helper\Jevix\JevixEasy::Parser($item->plus)?></p>
        <? endif ?>
        <? if(strlen($item->minus) > 0): ?>
            <p><span class="comment-title">Недостатки:</span> <?=\CMS\Core\Helper\Jevix\JevixEasy::Parser($item->minus)?></p>
        <? endif ?>
        <? if(strlen($item->text) > 0): ?>
            <p><span class="comment-title">Комментарий:</span> <?=\CMS\Core\Helper\Jevix\JevixEasy::Parser($item->text)?></p>
        <? endif ?>
    </div>
</article>