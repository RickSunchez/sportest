<select class="m-select-big b-select-option"
        onblur="changed_product(<?= $option->goods_id ?>)"
        name="options[<?= $option->pk() ?>]"
        data-option-id="<?= $option->pk() ?>">
    <? foreach ($variants as $variant): ?>
        <option value="<?= $variant->pk() ?>"><?= $variant->name ?></option>
    <? endforeach ?>
</select>