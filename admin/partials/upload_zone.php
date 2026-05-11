<?php
/**
 * Premium upload zone (drag & drop with preview)
 * Variables in scope:
 *   $name       — input name (e.g. 'cover_file' or 'files[]')
 *   $multiple   — bool, default false
 *   $current    — current image URL (for single-file mode), optional
 *   $hint       — short text under title
 *   $accept     — accept attribute, default 'image/*'
 */
$name      = $name      ?? 'file';
$multiple  = $multiple  ?? false;
$current   = $current   ?? '';
$hint      = $hint      ?? 'Arraste suas imagens aqui ou clique para selecionar · até 8MB cada · JPG · PNG · WEBP';
$accept    = $accept    ?? 'image/*';
$uid       = 'upz_' . bin2hex(random_bytes(3));
?>
<div class="upz" data-upz <?= $multiple ? 'data-multiple' : '' ?> id="<?= $uid ?>">
  <input type="file" name="<?= ee($name) ?>" <?= $multiple ? 'multiple' : '' ?> accept="<?= ee($accept) ?>">
  <div class="upz__inner">
    <span class="upz__ico"><i data-lucide="image-plus"></i></span>
    <strong class="upz__title">Solte aqui as imagens</strong>
    <span class="upz__hint"><?= ee($hint) ?></span>
    <span class="upz__pick">Selecionar arquivos</span>
  </div>
  <div class="upz__previews">
    <?php if ($current): ?>
      <div class="upz__preview" data-current>
        <img src="<?= ee($current) ?>" alt="">
        <button type="button" class="x" data-upz-current-remove aria-label="Remover foto principal"><i data-lucide="x"></i></button>
      </div>
    <?php endif; ?>
  </div>
  <div class="upz__progress"><i></i></div>
</div>
