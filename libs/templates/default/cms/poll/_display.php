<div class="b-widget b-vote-widget" id="widget_poll_<?= $poll->pk();?>" data-id="<?= $poll->pk();?>" >
    <div class="b-widget__body">
        <? if ($poll->text): ?>
            <div class="b-text b-vote-widget__text">
                <?= $poll->text ?>
            </div>
        <? endif; ?>
        <? if (count($items)): ?>
            <div class="b-vote-widget__radio">
                <? if (!$is_poll): ?>

                    <? foreach ($items as $item): ?>
                        <label class="b-vote-widget__label">
                            <input class="b-vote-widget__input" type="radio" name="item_id"
                                   value="<?= $item->pk(); ?>"/>
                            <span class="b-vote-widget__name"><?= $item->name ?></span>
                        </label>
                    <? endforeach; ?>

                <? else: ?>

                    <? foreach ($items as $item): ?>
                        <div class="b-vote-widget__item">
                            <?= $item->name ?> <span class="b-vote-widget__count">(<?= $item->str_vote() ?>)</span>
                        </div>
                    <? endforeach; ?>

                <? endif; ?>
            </div>
        <? endif; ?>
    </div>

    <div class="b-widget__footer">
        <? if (!$is_poll): ?>
            <button class="b-vote-widget__button" onclick="poll(this)" type="button">проголосовать</button>


            <script type="text/javascript">

                function poll(a) {

                    var item_id = $('input[name=item_id]:checked', '.b-vote-widget').val();
                    if (typeof(item_id) == 'undefined') {
                        alert('Выберите вариант');
                        return null;
                    }

                    var $btn = $(a);
                    $btn.fadeOut('slow');
                    $.ajax({
                        type: 'POST',
                        url: '<?= link_to('poll_data',array('action'=>'vote'))?>',
                        data: {id:<?= $poll->pk();?>, item_id: item_id},
                        success: function (response) {
                            if (response.error) {
                                alert(response.error);
                                $btn.fadeIn('slow');
                                return null;
                            }

                            if (response.ok) {
                                var html = '';

                                if (response.items.length) {
                                    $.each(response.items, function (index, item) {
                                        html += '<div class="b-vote-widget__item" >' + item.name + ' <span  class="b-vote-widget__count" >(' + item.vote + ')</span> </div>';
                                    });
                                } else {
                                    html = response.ok;
                                }
                                $('.b-vote-widget__radio', '.b-vote-widget').html(html);
                            }
                        },
                        error: function () {
                            $btn.fadeIn('slow');
                            alert('Серверная ошибка!')
                        }
                    })
                }
            </script>
        <? endif; ?>
    </div>
</div>