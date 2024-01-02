<select class="m-select-big b-product__options-additions__select"
        onchange="changed_option_addition(this)"
        name="options[<?= $option->pk() ?>]"
        data-option-id="<?= $option->pk() ?>"
        data-goods-id="<?= $option->goods_id ?>" >
    <? foreach ($variants as $variant): ?>
        <option value="<?= $variant->pk() ?>"><?= $variant->name ?></option>
    <? endforeach ?>
</select>