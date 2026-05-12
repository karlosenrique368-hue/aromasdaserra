<?php
$pageTitle='Taberna do Monge'; $pageDesc='Restaurante boutique aberto ao público, com gastronomia mediterrânea e vista para a serra.'; $pageSlug='taberna';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/partials.php';
$defaultAboutGallery = json_encode([
  ['src'=>'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85','caption'=>'Salão Taberna'],
  ['src'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85','caption'=>'Mesa'],
  ['src'=>'https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=1200&q=85','caption'=>'Prato'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
$aboutGallery = block('taberna','about_gallery','');
if ($aboutGallery === '') {
  $aboutGallery = json_encode([
    ['src'=>block('taberna','about_image_1','https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85'),'caption'=>'Salão Taberna'],
    ['src'=>block('taberna','about_image_2','https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85'),'caption'=>'Mesa'],
    ['src'=>block('taberna','about_image_3','https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=1200&q=85'),'caption'=>'Prato'],
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: $defaultAboutGallery;
}
?>
<section class="page-hero">
  <img class="page-hero-img kenburns" src="<?= e(repair_image_url(block('taberna','hero_image','https://images.unsplash.com/photo-1559717865-a99cac1c95d8?auto=format&fit=crop&w=2000&q=80'))) ?>" alt="Taberna do Monge">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('taberna','hero_eyebrow','Restaurante boutique')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] text-reveal"><?= block('taberna','hero_title','Taberna <em class="serif-italic text-gold-500">do Monge.</em>') ?></h1>
    <p class="mt-4 max-w-2xl text-cream-100/85 text-lg reveal"><?= block('taberna','hero_subtitle','Um convite à mesa farta, mediterrânea, generosa, harmonizada com vinhos especiais e a vista da serra.') ?></p>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
    <div class="reveal rounded-md overflow-hidden shadow-xl">
      <?php embla_carousel(gallery_slides($aboutGallery, 'Taberna do Monge'), ['ratio'=>'4/5','autoplay'=>true,'lightbox'=>true,'group'=>'taberna-hero']); ?>
    </div>
    <div class="reveal">
      <span class="eyebrow"><?= e(block('taberna','about_eyebrow','Sobre a casa')) ?></span>
      <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-3 leading-tight"><?= block('taberna','about_title','Aberta ao <em class="serif-italic text-terracota-500">público.</em>') ?></h2>
      <p class="mt-6 text-[17px] leading-[1.9] text-ink-700/90"><?= block('taberna','about_body','A Taberna do Monge é o restaurante boutique da pousada, aberto também a visitantes externos. Nasceu como um projeto afetivo dos diretores para reunir boa mesa, memória, conversa sem pressa e o clima acolhedor da serra. Receitas autorais, vinhos selecionados e uma lareira central fazem do espaço um dos encontros mais especiais da Aromas da Serra.') ?></p>
      <ul class="mt-7 space-y-3 text-[15px]">
        <li class="flex items-center gap-3"><i data-lucide="clock" class="w-5 h-5 text-terracota-500"></i> <?= e(block('taberna','bullet_1','Aberto para almoços e jantares · reservas recomendadas')) ?></li>
        <li class="flex items-center gap-3"><i data-lucide="users" class="w-5 h-5 text-terracota-500"></i> <?= e(block('taberna','bullet_2','Espaço íntimo · capacidade limitada')) ?></li>
        <li class="flex items-center gap-3"><i data-lucide="flame" class="w-5 h-5 text-terracota-500"></i> <?= e(block('taberna','bullet_3','Temporada de fondues no inverno')) ?></li>
      </ul>
      <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-primary magnetic mt-8"><i data-lucide="calendar" class="w-4 h-4"></i> Reservar mesa</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
