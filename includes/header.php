<?php
require_once __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? SITE_NAME;
$pageDesc  = $pageDesc  ?? 'Refúgio boutique em Mar Vermelho — Suíça Alagoana. Gastronomia mediterrânea, contemplação e hospitalidade exclusiva para adultos.';
$pageSlug  = $pageSlug  ?? 'home';
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
<title><?= e($pageTitle) ?> · <?= e(SITE_NAME) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<meta name="theme-color" content="#2F4A2A">
<meta property="og:title" content="<?= e($pageTitle) ?> · <?= e(SITE_NAME) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:type" content="website">
<link rel="icon" type="image/jpeg" href="<?= asset('img/logoserra.jpg') ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://images.unsplash.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Inter:wght@300;400;500;600&family=Italiana&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        forest:    { 50:'#f3f6f1', 100:'#e3ebde', 500:'#5a7a4d', 700:'#3a5b30', 800:'#2f4a2a', 900:'#1f3019' },
        terracota: { 400:'#c8956a', 500:'#b8754a', 600:'#9a5e3a' },
        cream:     { 50:'#faf6ef', 100:'#f4ece0', 200:'#ebdcc4', 300:'#dcc59c' },
        gold:      { 500:'#c4a46c', 600:'#a98955' },
        ink:       { 700:'#3b342c', 800:'#2a2520', 900:'#1a1612' },
      },
      fontFamily: {
        display:  ['"Cormorant Garamond"', 'serif'],
        editorial:['"Italiana"', 'serif'],
        sans:     ['Inter', 'system-ui', 'sans-serif'],
      },
      letterSpacing: { eyebrow: '0.32em' },
      maxWidth: { '8xl': '88rem' },
    }
  }
}
</script>
<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
<link rel="stylesheet" href="<?= asset('css/premium.css') ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script defer src="https://unpkg.com/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>
<script defer src="https://unpkg.com/embla-carousel/embla-carousel.umd.js"></script>
<script defer src="https://unpkg.com/embla-carousel-autoplay/embla-carousel-autoplay.umd.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.14.9/dist/cdn.min.js"></script>
<script defer src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-cream-50 text-ink-800 antialiased font-sans selection:bg-forest-800 selection:text-cream-50" data-page="<?= e($pageSlug) ?>">

<a href="#conteudo" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[200] focus:bg-forest-800 focus:text-cream-50 focus:px-4 focus:py-2 focus:rounded">Pular para o conteúdo</a>

<!-- Preloader -->
<div id="preloader" class="fixed inset-0 z-[300] bg-cream-50 grid place-items-center transition-opacity duration-700">
  <div class="text-center">
    <img src="<?= asset('img/logoserra.jpg') ?>" alt="" class="w-24 h-24 mx-auto opacity-90 object-contain rounded-full">
    <div class="mt-4 h-px w-32 mx-auto bg-gold-500/30 overflow-hidden relative">
      <span class="absolute inset-0 bg-forest-800 animate-[loaderbar_1.4s_ease-in-out_infinite]"></span>
    </div>
  </div>
</div>

<!-- Topbar -->
<div class="hidden md:block bg-forest-900 text-cream-100/85 text-[12px] tracking-wide">
  <div class="max-w-8xl mx-auto px-6 h-9 flex items-center justify-between">
    <div class="flex items-center gap-5">
      <span class="inline-flex items-center gap-2"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-gold-500"></i> <?= e(SITE_LOCATION) ?></span>
      <span class="inline-flex items-center gap-2"><i data-lucide="leaf" class="w-3.5 h-3.5 text-gold-500"></i> Exclusivo para adultos</span>
    </div>
    <div class="flex items-center gap-5">
      <a href="tel:+<?= SITE_PHONE_RAW ?>" class="hover:text-gold-500 transition"><?= e(SITE_PHONE_DISPLAY) ?></a>
      <a href="<?= e(SITE_INSTAGRAM) ?>" target="_blank" rel="noopener" aria-label="Instagram" class="hover:text-gold-500"><i data-lucide="instagram" class="w-4 h-4"></i></a>
      <a href="<?= e(SITE_FACEBOOK) ?>"  target="_blank" rel="noopener" aria-label="Facebook"  class="hover:text-gold-500"><i data-lucide="facebook"  class="w-4 h-4"></i></a>
    </div>
  </div>
</div>

<!-- Header -->
<header x-data="{ open:false, scrolled:false }"
  data-mobile-menu-root
  @scroll.window="scrolled = window.scrollY > 24"
  :class="scrolled ? 'shadow-[0_1px_0_0_rgba(60,50,40,.08)]' : 'shadow-none'"
  class="sticky top-0 z-[100] bg-cream-50/95 backdrop-blur transition-colors duration-300">
  <div class="max-w-8xl mx-auto px-6 h-20 flex items-center justify-between">
    <a href="<?= url('') ?>" class="flex items-center gap-3 group">
      <span class="brand-mark brand-mark--lg"><img src="<?= asset('img/logoserra.jpg') ?>" alt="<?= e(SITE_NAME) ?>"></span>
      <span class="leading-tight hidden sm:block sr-only">
        <span class="block font-editorial text-[19px] text-forest-900">Aromas da Serra</span>
      </span>
    </a>

    <nav class="hidden lg:flex items-center gap-1" aria-label="Principal">
      <?php foreach ($NAV as $item): $hasKids = !empty($item['children']); ?>
        <?php if ($hasKids): ?>
          <div class="relative" x-data="{m:false}" @mouseenter="m=true" @mouseleave="m=false">
            <a href="<?= e($item['href']) ?>" class="px-3 py-2 text-[13px] tracking-wide uppercase font-medium text-ink-800 hover:text-forest-800 inline-flex items-center gap-1 transition">
              <?= e($item['label']) ?> <i data-lucide="chevron-down" class="w-3.5 h-3.5 mt-px transition-transform" :class="m && 'rotate-180'"></i>
            </a>
            <div x-show="m" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute top-full left-0 min-w-[260px] pt-3">
              <div class="bg-cream-50 border border-cream-200 shadow-2xl rounded-md py-2">
                <?php foreach ($item['children'] as $sub): ?>
                  <a href="<?= e($sub['href']) ?>" class="block px-5 py-2.5 text-[14px] text-ink-800 hover:bg-cream-100 hover:text-forest-800 transition"><?= e($sub['label']) ?></a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php else: ?>
          <a href="<?= e($item['href']) ?>" class="px-3 py-2 text-[13px] tracking-wide uppercase font-medium text-ink-800 hover:text-forest-800 transition"><?= e($item['label']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>

    <div class="hidden lg:flex items-center gap-3">
      <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-primary magnetic">
        <i data-lucide="calendar-heart" class="w-4 h-4"></i> Reservar
      </a>
    </div>

    <button @click="open=true" data-mobile-menu-open class="lg:hidden w-11 h-11 grid place-items-center text-forest-900" aria-label="Abrir menu" aria-expanded="false">
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
  </div>

  <!-- Mobile menu fullscreen editorial -->
  <div x-show="open" x-cloak data-mobile-menu-panel aria-hidden="true" x-transition:enter="transition ease-out duration-400" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="lg:hidden">
    <div class="mm">
      <div class="mm-enter h-full flex flex-col">
        <div class="mm-head">
          <a href="<?= url('') ?>" class="flex items-center gap-2"><span class="brand-mark" style="width:54px;height:54px;"><img src="<?= asset('img/logoserra.jpg') ?>" alt=""></span></a>
          <button @click="open=false" data-mobile-menu-close class="w-10 h-10 grid place-items-center text-ink-800" aria-label="Fechar menu"><i data-lucide="x" class="w-6 h-6"></i></button>
        </div>

        <nav class="mm-list">
          <?php $i=1; foreach ($NAV as $item): ?>
            <a href="<?= e($item['href']) ?>" class="mm-item">
              <span><?= e($item['label']) ?></span>
              <span class="num">0<?= $i ?></span>
            </a>
            <?php if (!empty($item['children'])): ?>
              <div class="mm-sub">
                <?php foreach ($item['children'] as $sub): ?>
                  <a href="<?= e($sub['href']) ?>"><?= e($sub['label']) ?></a>
                <?php endforeach; ?>
              </div>
            <?php endif; $i++; endforeach; ?>
        </nav>

        <div class="mm-foot">
          <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-primary w-full justify-center">
            <i data-lucide="message-circle" class="w-4 h-4"></i> Reservar pelo WhatsApp
          </a>
          <div class="grid grid-cols-3 gap-3 text-center text-[12px]">
            <a href="tel:+<?= SITE_PHONE_RAW ?>" class="py-3 border border-cream-200 rounded-md text-ink-800"><i data-lucide="phone" class="w-4 h-4 mx-auto mb-1 text-terracota-500"></i>Ligar</a>
            <a href="<?= e(SITE_INSTAGRAM) ?>" target="_blank" rel="noopener" class="py-3 border border-cream-200 rounded-md text-ink-800"><i data-lucide="instagram" class="w-4 h-4 mx-auto mb-1 text-terracota-500"></i>Instagram</a>
            <a href="mailto:<?= e(SITE_EMAIL) ?>" class="py-3 border border-cream-200 rounded-md text-ink-800"><i data-lucide="mail" class="w-4 h-4 mx-auto mb-1 text-terracota-500"></i>E-mail</a>
          </div>
          <p class="text-center text-[11px] tracking-eyebrow uppercase text-ink-700/60 pt-2"><?= e(SITE_LOCATION) ?> · Suíça Alagoana</p>
        </div>
      </div>
    </div>
  </div>
</header>

<main id="conteudo">
