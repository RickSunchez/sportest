<? if($users):?>
<div class="dialogs_list">
<? foreach($users as $user):?>
    <? foreach ($attr_name as $k => $attr) {
        if (in_array($attr->code, array('surname', 'name'))) {
            $name .= $user_attrs[$user->pk()][$attr->pk()] . ' ';
        } else {
            $attrs .= '<div class="attr_name">' . $attr->name . ': <span>' . $user_attrs[$user->pk()][$attr->pk()] . '</span></div>';
        }
    }?>
    <a href="<?= link_to('im_private_message', array('id' => $user->pk())); ?>">
        <?= $name ?>
        <? if( $new[$user->pk()]==\CMS\Users\Entity\Message::STATUS_NEW ):?>
            <span color="text_normal _color_black">new</span>
        <? endif;?>
    </a>
<? endforeach;?>
</div>
<? else:?>
    <div class="padding_tb_10">Список диалогов пуст!</div>
<? endif;?>