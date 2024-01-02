<? foreach ($variants as $key=>$variant): ?>
    <label for="variant_<?=$variant->pk()?>">
        <input <?= $key==0?'checked':''?>  id="variant_<?=$variant->pk()?>" type="radio" name="options[<?= $option->pk() ?>]" value="<?= $variant->pk() ?>" />
        <?= $variant->name ?>
    </label>
<? endforeach ?>


