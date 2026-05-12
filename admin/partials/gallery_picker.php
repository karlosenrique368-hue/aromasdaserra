<?php
/**
 * Premium gallery picker.
 * Variables in scope:
 *   $items           array|string current gallery items
 *   $inputName       hidden item input name, default gallery_urls[]
 *   $uploadName      file input name, default gallery_files[]
 *   $fileCaptionName uploaded file caption input name, default gallery_file_captions[]
 *   $hint            helper text
 */
$items = $items ?? [];
if (is_string($items)) $items = image_items_to_array($items);
$items = array_values(array_filter(array_map(fn($item) => normalize_public_image_item($item), (array)$items)));
$inputName = $inputName ?? 'gallery_urls[]';
$uploadName = $uploadName ?? 'gallery_files[]';
$fileCaptionName = $fileCaptionName ?? 'gallery_file_captions[]';
$hint = $hint ?? 'Envie novas imagens ou adicione uma imagem externa individualmente.';
?>
<div class="gallery-picker" data-gallery-picker data-input-name="<?= ee($inputName) ?>" data-file-caption-name="<?= ee($fileCaptionName) ?>">
  <div class="gallery-picker__head">
    <div>
      <strong>Galeria</strong>
      <span><?= ee($hint) ?> Arraste as imagens para escolher a primeira e reorganizar a sequência.</span>
    </div>
    <label class="gallery-picker__upload">
      <i data-lucide="image-plus"></i>
      <span>Enviar imagens</span>
      <input type="file" name="<?= ee($uploadName) ?>" accept="image/*" multiple data-gallery-files>
    </label>
  </div>

  <div class="gallery-picker__grid" data-gallery-list>
    <?php foreach ($items as $index => $item): ?>
      <?php $hiddenValue = json_encode($item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: ''; ?>
      <div class="gallery-picker__item" data-gallery-item draggable="true">
        <div class="gallery-picker__media">
          <img src="<?= ee($item['src']) ?>" alt="">
          <button type="button" class="gallery-picker__drag" data-gallery-drag aria-label="Arrastar imagem"><i data-lucide="grip-vertical"></i></button>
          <span class="gallery-picker__order" data-gallery-order><?= $index + 1 ?></span>
          <button type="button" class="gallery-picker__remove" data-gallery-remove aria-label="Remover imagem"><i data-lucide="x"></i></button>
        </div>
        <input type="hidden" name="<?= ee($inputName) ?>" value="<?= ee($hiddenValue) ?>" data-gallery-value>
        <input type="text" class="gallery-picker__caption" value="<?= ee($item['caption'] ?? '') ?>" placeholder="Nome da comida / legenda" data-gallery-caption>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="gallery-picker__add">
    <input type="text" data-gallery-url placeholder="Adicionar imagem externa por URL">
    <button type="button" class="btn btn-ghost" data-gallery-add><i data-lucide="plus"></i> Adicionar</button>
  </div>
</div>