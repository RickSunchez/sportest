<div>
    <form action="<?= link_to('im_data',array('action'=>'addMessage'))?>" method="post">
        <textarea name="text" ></textarea>
        <input type="hidden" name="to_id"  value="<?=$user->pk()?>"/>
        <button type="submit" >Отправить</button>
    </form>
</div>

<?foreach($messages as $mgs):?>
    <div>
        <?= $mgs->text?>
    </div>
<?endforeach;?>