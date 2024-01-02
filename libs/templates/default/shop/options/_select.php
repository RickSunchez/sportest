<select name="options[<?= $option->pk() ?>]">
    <? foreach ($variants as $variant): ?>
        <option value="<?= $variant->pk() ?>"><?= $variant->name ?></option>
    <? endforeach ?>
</select>