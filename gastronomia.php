<?php
$pageTitle='Gastronomia'; $pageDesc='Cozinha mediterrânea autoral em Mar Vermelho, com sabores da Suíça, sul da França, Itália e Mediterrâneo.'; $pageSlug='gastronomia';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/partials.php';
$defaultCarouselGallery = json_encode([
  ['src'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85','caption'=>'Café da manhã'],
  ['src'=>'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85','caption'=>'Fondue'],
  ['src'=>'https://images.unsplash.com/photo-1473093226795-af9932fe5856?auto=format&fit=crop&w=1200&q=85','caption'=>'Prato autoral'],
  ['src'=>'https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=1200&q=85','caption'=>'Massa caseira'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
$defaultFoodGallery = json_encode([
  ['src'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=900&q=80','caption'=>'Café da manhã'],
  ['src'=>'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=900&q=80','caption'=>'Fondue'],
  ['src'=>'https://images.unsplash.com/photo-1473093226795-af9932fe5856?auto=format&fit=crop&w=900&q=80','caption'=>'Prato autoral'],
  ['src'=>'https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=900&q=80','caption'=>'Massa caseira'],
  ['src'=>'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=900&q=80','caption'=>'Pães rústicos'],
  ['src'=>'https://images.unsplash.com/photo-1510626176961-4b57d4fbad03?auto=format&fit=crop&w=900&q=80','caption'=>'Vinhos'],
  ['src'=>'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=900&q=80','caption'=>'Doces'],
  ['src'=>'https://images.unsplash.com/photo-1466637574441-749b8f19452f?auto=format&fit=crop&w=900&q=80','caption'=>'Frutas frescas'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
$carouselGallery = block('gastronomia','carousel_gallery','');
if ($carouselGallery === '') {
  $carouselGallery = json_encode([
    ['src'=>block('gastronomia','carousel_image_1','https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85'),'caption'=>'Café da manhã'],
    ['src'=>block('gastronomia','carousel_image_2','https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85'),'caption'=>'Fondue'],
    ['src'=>block('gastronomia','carousel_image_3','https://images.unsplash.com/photo-1473093226795-af9932fe5856?auto=format&fit=crop&w=1200&q=85'),'caption'=>'Prato autoral'],
    ['src'=>block('gastronomia','carousel_image_4','https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=1200&q=85'),'caption'=>'Massa caseira'],
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: $defaultCarouselGallery;
}
$foodGallery = block('gastronomia','food_gallery',$defaultFoodGallery);
?>
<section class="page-hero">
  <img class="page-hero-img kenburns" src="<?= e(block('gastronomia','hero_image','https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=2000&q=80')) ?>" alt="Gastronomia">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('gastronomia','hero_eyebrow','Cozinha Mediterrânea')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] text-reveal"><?= block('gastronomia','hero_title','Sabores que <em class="serif-italic text-gold-500">acolhem.</em>') ?></h1>
    <p class="mt-4 max-w-2xl text-cream-100/85 text-lg reveal"><?= block('gastronomia','hero_subtitle','Receitas autorais inspiradas em uma jornada culinária pela Suíça, sul da França, Itália e Mediterrâneo.') ?></p>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
    <div class="reveal">
      <span class="eyebrow"><?= e(block('gastronomia','philosophy_eyebrow','A nossa filosofia')) ?></span>
      <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-3 leading-tight"><?= block('gastronomia','philosophy_title','A mesa é o <em class="serif-italic text-terracota-500">coração</em> da pousada.') ?></h2>
      <p class="mt-6 text-[17px] leading-[1.9] text-ink-700/90"><?= block('gastronomia','philosophy_body','Combinando ingredientes frescos e temperos colhidos diretamente da nossa Mandala, cada prato é uma alquimia de sabores e aromas. Mesa farta, vinhos especiais e tempo desacelerado.') ?></p>
      <ul class="mt-7 grid sm:grid-cols-2 gap-3 text-[15px]">
        <li class="flex items-start gap-2"><i data-lucide="wheat" class="w-4 h-4 mt-1 text-gold-600"></i> <?= e(block('gastronomia','bullet_1','Pães rústicos artesanais')) ?></li>
        <li class="flex items-start gap-2"><i data-lucide="wine" class="w-4 h-4 mt-1 text-gold-600"></i> <?= e(block('gastronomia','bullet_2','Carta de vinhos curada')) ?></li>
        <li class="flex items-start gap-2"><i data-lucide="flame" class="w-4 h-4 mt-1 text-gold-600"></i> <?= e(block('gastronomia','bullet_3','Temporada de fondues')) ?></li>
        <li class="flex items-start gap-2"><i data-lucide="sprout" class="w-4 h-4 mt-1 text-gold-600"></i> <?= e(block('gastronomia','bullet_4','Ervas da Mandala')) ?></li>
      </ul>
    </div>
    <div class="reveal rounded-md overflow-hidden shadow-xl">
      <?php embla_carousel(gallery_slides($carouselGallery, 'Gastronomia'), ['ratio'=>'4/5','autoplay'=>true,'lightbox'=>true,'group'=>'gastro-hero']); ?>
    </div>
  </div>
</section>

<section class="section bg-cream-100 paper">
  <div class="max-w-7xl mx-auto px-6">
    <header class="reveal text-center max-w-2xl mx-auto">
      <span class="eyebrow"><?= e(block('gastronomia','gallery_eyebrow','Galeria')) ?></span>
      <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-3 leading-tight"><?= block('gastronomia','gallery_title','Aromas, cores e <em class="serif-italic text-terracota-500">texturas.</em>') ?></h2>
    </header>
    <div class="mt-12 grid grid-cols-2 lg:grid-cols-4 gap-4 reveal-stagger">
      <?php
      $imgs = gallery_slides($foodGallery, 'Gastronomia');
      foreach($imgs as $item): $src=repair_image_url((string)$item['src']); $cap=(string)($item['caption'] ?: $item['alt']); ?>
        <a href="<?= e($src) ?>" class="glightbox gallery-tile aspect-square block" data-gallery="gastro" data-type="image" data-description="<?= e($cap) ?>">
          <img src="<?= e($src) ?>" alt="<?= e($cap) ?>" class="w-full h-full object-cover" loading="lazy">
          <span class="gallery-action"><i data-lucide="maximize-2" class="w-3.5 h-3.5"></i> Abrir galeria</span>
          <figcaption><?= e($cap) ?></figcaption>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
