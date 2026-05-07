<?php
/**
 * Embla carousel partial
 * @param array $slides  array of ['src'=>url, 'alt'=>string, 'caption'=>?]
 * @param array $opts    ['ratio'=>'4/5'|'4/3'|'1/1', 'autoplay'=>bool, 'dots'=>bool, 'arrows'=>bool, 'lightbox'=>bool, 'group'=>str]
 */
function embla_carousel(array $slides, array $opts = []): void {
    $ratio    = $opts['ratio']    ?? '4/5';
    $autoplay = $opts['autoplay'] ?? true;
    $dots     = $opts['dots']     ?? true;
    $arrows   = $opts['arrows']   ?? true;
    $lightbox = $opts['lightbox'] ?? false;
    $group    = $opts['group']    ?? 'gallery-' . substr(md5(serialize($slides)), 0, 6);
    ?>
    <div class="embla relative" data-autoplay="<?= $autoplay ? 'true' : 'false' ?>">
      <div class="embla__viewport">
        <div class="embla__container">
          <?php foreach ($slides as $s): ?>
            <div class="embla__slide" style="aspect-ratio: <?= e($ratio) ?>;">
              <?php if ($lightbox): ?>
                <a href="<?= e($s['src']) ?>" class="glightbox block w-full h-full" data-gallery="<?= e($group) ?>" data-description="<?= e($s['caption'] ?? '') ?>">
                  <img src="<?= e($s['src']) ?>" alt="<?= e($s['alt'] ?? '') ?>" class="w-full h-full object-cover" loading="lazy">
                </a>
              <?php else: ?>
                <img src="<?= e($s['src']) ?>" alt="<?= e($s['alt'] ?? '') ?>" class="w-full h-full object-cover" loading="lazy">
              <?php endif; ?>
              <?php if (!empty($s['caption'])): ?>
                <span class="absolute left-4 bottom-4 z-[1] bg-cream-50/95 text-ink-900 text-[11px] tracking-eyebrow uppercase px-3 py-1 rounded-full"><?= e($s['caption']) ?></span>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php if ($arrows): ?>
        <button class="embla__btn embla__btn--prev" aria-label="Anterior" type="button"><i data-lucide="chevron-left" class="w-5 h-5"></i></button>
        <button class="embla__btn embla__btn--next" aria-label="Próximo"  type="button"><i data-lucide="chevron-right" class="w-5 h-5"></i></button>
      <?php endif; ?>
      <?php if ($dots): ?><div class="embla__dots"></div><?php endif; ?>
    </div>
    <?php
}
