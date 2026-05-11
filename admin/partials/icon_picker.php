<?php
/**
 * Visual Lucide icon picker.
 * Variables in scope:
 *   $iconName   input name
 *   $iconValue  selected icon name
 */
$iconName = $iconName ?? 'icon';
$iconValue = $iconValue ?: 'sparkles';
$iconOptions = [
  'sparkles','flame','coffee','glass-water','wine','cooking-pot','utensils','utensils-crossed','chef-hat','wheat',
  'leaf','sprout','flower-2','trees','tree-pine','mountain','mountain-snow','waves','footprints','route','map',
  'home','heart','hand-heart','calendar-heart','book-open','quote','shopping-bag','image','palette','sun','moon',
  'star','gem','music','users','clock','message-circle','mail','phone','map-pin','compass','camera','landmark'
];
if (!in_array($iconValue, $iconOptions, true)) array_unshift($iconOptions, $iconValue);
?>
<div class="icon-picker" data-icon-picker>
  <input type="hidden" name="<?= ee($iconName) ?>" value="<?= ee($iconValue) ?>" data-icon-value>
  <div class="icon-picker__preview" aria-live="polite">
    <span><i data-lucide="<?= ee($iconValue) ?>"></i></span>
    <strong>Ícone selecionado</strong>
  </div>
  <div class="icon-picker__grid" role="listbox" aria-label="Escolha um ícone">
    <?php foreach ($iconOptions as $icon): ?>
      <button type="button" class="icon-picker__option <?= $icon === $iconValue ? 'is-selected' : '' ?>" data-icon-choice="<?= ee($icon) ?>" aria-label="Ícone <?= ee($icon) ?>" aria-pressed="<?= $icon === $iconValue ? 'true' : 'false' ?>" title="<?= ee($icon) ?>">
        <i data-lucide="<?= ee($icon) ?>"></i>
      </button>
    <?php endforeach; ?>
  </div>
</div>