<?php
$pageTitle='Localização'; $pageDesc='Mar Vermelho, Alagoas. Localizada na região serrana, conhecida como Suíça Alagoana.'; $pageSlug='localizacao';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <img class="page-hero-img" src="<?= e(block('localizacao','hero_image','https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=2000&q=80')) ?>" alt="Vista da serra">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('localizacao','hero_eyebrow','A nossa')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05]"><?= block('localizacao','hero_title','Localização <em class="serif-italic text-gold-500">em Mar Vermelho.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg"><?= block('localizacao','hero_subtitle','Região serrana de Alagoas, a chamada <em class="serif-italic">Suíça Alagoana</em>.') ?></p>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-6xl mx-auto px-6 grid lg:grid-cols-[1fr,1.4fr] gap-12 items-start">
    <aside class="reveal space-y-6">
      <div>
        <span class="eyebrow"><?= e(block('localizacao','address_eyebrow','Endereço')) ?></span>
        <p class="font-editorial text-3xl text-forest-900 mt-3"><?= block('localizacao','address_title','Mar Vermelho<br><span class="serif-italic text-terracota-500">Alagoas, Brasil</span>') ?></p>
      </div>
      <div class="grid gap-3 text-[15px]">
        <div class="flex items-start gap-3"><i data-lucide="phone" class="w-4 h-4 mt-1 text-gold-600"></i><a href="tel:+<?= SITE_PHONE_RAW ?>" class="hover:text-forest-800"><?= e(SITE_PHONE_DISPLAY) ?></a></div>
        <div class="flex items-start gap-3"><i data-lucide="mail" class="w-4 h-4 mt-1 text-gold-600"></i><a href="mailto:<?= e(SITE_EMAIL) ?>" class="hover:text-forest-800"><?= e(SITE_EMAIL) ?></a></div>
        <div class="flex items-start gap-3"><i data-lucide="message-circle" class="w-4 h-4 mt-1 text-gold-600"></i><a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="hover:text-forest-800">WhatsApp para reservas</a></div>
      </div>
      <a href="https://www.google.com/maps/search/?api=1&query=Mar+Vermelho+Alagoas" target="_blank" rel="noopener" class="btn-primary"><i data-lucide="map" class="w-4 h-4"></i> Abrir no Google Maps</a>
      <a href="<?= url('itinerario.php') ?>" class="btn-ghost block w-fit"><i data-lucide="route" class="w-4 h-4"></i> Ver itinerário até a Serra</a>
    </aside>

    <div class="reveal">
      <div class="rounded-md overflow-hidden shadow-xl border border-cream-200">
        <iframe
          src="https://www.google.com/maps?q=Mar+Vermelho,+Alagoas&output=embed"
          width="100%" height="480" style="border:0" loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          title="Mapa Mar Vermelho - Alagoas"></iframe>
      </div>
    </div>
  </div>
</section>

<section class="section bg-cream-100 paper">
  <div class="max-w-5xl mx-auto px-6 reveal text-center">
    <span class="eyebrow"><?= e(block('localizacao','region_eyebrow','A região')) ?></span>
    <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-4 leading-tight"><?= block('localizacao','region_title','Um lugar de <em class="serif-italic text-terracota-500">beleza incomparável.</em>') ?></h2>
    <p class="mt-6 text-[17px] leading-[1.9] text-ink-700/90">
      <?= block('localizacao','region_body_1','Localizado na região serrana de Alagoas, no município de Mar Vermelho. Durante o nascer ou pôr do sol, os amantes da contemplação podem apreciar uma paisagem espetacular, com tons de verde e floral que se misturam em um céu deslumbrante.') ?>
    </p>
    <p class="mt-4 text-[17px] leading-[1.9] text-ink-700/90">
      <?= block('localizacao','region_body_2','Mar Vermelho é um destino perfeito para quem busca momentos aconchegantes, chocolates quentes, fondues de queijo e bons vinhos em volta da lareira.') ?>
    </p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
