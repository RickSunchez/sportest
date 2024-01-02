<? foreach ($variants as $key => $variant): ?>
    <? if ($key == 0): ?>
        <label for="variant_<?= $variant->pk() ?>">

            <input id="variant_<?= $variant->pk() ?>"
                   type="checkbox"
                   name="options[<?= $option->pk()?>]"
                   value="<?= $variant->pk() ?>"/> <?= $variant->modifier?> <?= $variant->pk()?>
        </label>
    <? endif; ?>
<? endforeach ?>