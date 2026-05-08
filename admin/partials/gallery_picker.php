<?php
/**
 * Premium gallery picker.
 * Variables in scope:
 *   $items       array|string current gallery URLs
 *   $inputName   hidden URL input name, default gallery_urls[]
 *   $uploadName  file input name, default gallery_files[]
 *   $hint        helper text
 */
$items = $items ?? [];
if (is_string($items)) $items = image_list_to_array($items);
$inputName = $inputName ?? 'gallery_urls[]';
$uploadName = $uploadName ?? 'gallery_files[]';
$hint = $hint ?? 'Envie novas imagens ou adicione uma imagem externa individualmente.';
?>
<div class="gallery-picker" data-gallery-picker data-input-name="<?= ee($inputName) ?>">
  <div class="gallery-picker__head">
    <div>
      <strong>Galeria</strong>
      <span><?= ee($hint) ?></span>
    </div>
    <label class="gallery-picker__upload">
      <i data-lucide="image-plus"></i>
      <span>Enviar imagens</span>
      <input type="file" name="<?= ee($uploadName) ?>" accept="image/*" multiple data-gallery-files>
    </label>
  </div>

  <div class="gallery-picker__grid" data-gallery-list>
    <?php foreach ($items as $url): ?>
      <div class="gallery-picker__item" data-gallery-item>
        <img src="<?= ee($url) ?>" alt="">
        <input type="hidden" name="<?= ee($inputName) ?>" value="<?= ee($url) ?>">
        <button type="button" data-gallery-remove aria-label="Remover imagem"><i data-lucide="x"></i></button>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="gallery-picker__add">
    <input type="text" data-gallery-url placeholder="Adicionar imagem externa por URL">
    <button type="button" class="btn btn-ghost" data-gallery-add><i data-lucide="plus"></i> Adicionar</button>
  </div>
</div>