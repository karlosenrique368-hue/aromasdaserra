<?php
$pageTitle='A Pousada'; $pageDesc='Conheça a história e a essência da Pousada Aromas da Serra, refúgio em Mar Vermelho — Alagoas.'; $pageSlug='a-pousada';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <img class="page-hero-img" src="<?= e(block('a-pousada','hero_image','https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=2000&q=80')) ?>" alt="Vista da serra alagoana">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('a-pousada','hero_eyebrow','Sobre nós')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] max-w-3xl"><?= block('a-pousada','hero_title','A Pousada <em class="serif-italic text-gold-500">Aromas da Serra.</em>') ?></h1>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-5xl mx-auto px-6 grid md:grid-cols-[1fr,2fr] gap-12 items-start reveal">
    <aside class="md:sticky md:top-28">
      <span class="eyebrow">Nossa essência</span>
      <p class="font-editorial text-3xl text-forest-900 mt-4 leading-tight">Sabores, cores e bem-estar entre as serras de Alagoas.</p>
    </aside>
    <div class="space-y-5 text-[17px] leading-[1.9] text-ink-700/90">
      <p><?= block('a-pousada','intro_body','Na Pousada <strong class="text-forest-800">Aromas da Serra</strong>, os hóspedes são transportados para um lugar onde é possível desfrutar de uma experiência única — onde a natureza e a contemplação são os ingredientes principais.') ?></p>
      <p>Localizada entre as serras de Alagoas e com uma culinária sofisticada de influência europeia, a pousada oferece uma verdadeira viagem de sabores, cores e bem-estar.</p>
      <p>A paisagem exuberante e a simplicidade do local criam um ambiente acolhedor, onde é possível sentir uma paz única e verdadeira. É um convite para quem deseja se conectar com amigos e consigo — em um lugar de cheiros, brisa do campo, vinho e música boa.</p>
      <p class="serif-italic text-forest-800 text-[19px]">Importante: traga um bom calçado e agasalho para aproveitar o clima frio da nossa <span class="text-terracota-500">"Suíça Alagoana"</span>.</p>
    </div>
  </div>
</section>

<section class="section bg-cream-100 paper">
  <div class="max-w-6xl mx-auto px-6 reveal">
    <div class="grid md:grid-cols-2 gap-12 items-center">
      <img src="https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=1100&q=80" alt="Detalhe da pousada" class="w-full aspect-[4/5] object-cover rounded-md shadow-xl">
      <div>
        <span class="eyebrow">Para adultos</span>
        <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-4 leading-tight">Uma proposta de <em class="serif-italic text-terracota-500">relaxamento</em> em meio ao silêncio.</h2>
        <p class="mt-6 text-[17px] leading-[1.9] text-ink-700/90">Exclusivamente para adultos, não dispomos de atrativos infantis nem TVs nos quartos. Nossa culinária é exótica e experimental, sempre em harmonização com vinhos especiais.</p>
        <p class="mt-4 text-[17px] leading-[1.9] text-ink-700/90 serif-italic">Digamos que nossos hóspedes fazem uma linda viagem para outros países sem precisar sair de nossa querida Alagoas.</p>
      </div>
    </div>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-6xl mx-auto px-6 reveal-stagger grid md:grid-cols-3 gap-8 text-center">
    <div><i data-lucide="mountain-snow" class="w-9 h-9 mx-auto text-terracota-500"></i><h3 class="font-editorial text-2xl mt-4 text-forest-900">Clima serrano</h3><p class="mt-2 text-ink-700/80">Temperaturas amenas e brisa do campo durante todo o ano.</p></div>
    <div><i data-lucide="users" class="w-9 h-9 mx-auto text-terracota-500"></i><h3 class="font-editorial text-2xl mt-4 text-forest-900">Adults only</h3><p class="mt-2 text-ink-700/80">Atmosfera contemplativa, projetada para o descanso profundo.</p></div>
    <div><i data-lucide="sparkles" class="w-9 h-9 mx-auto text-terracota-500"></i><h3 class="font-editorial text-2xl mt-4 text-forest-900">Hospitalidade</h3><p class="mt-2 text-ink-700/80">Atendimento atento e personalizado para cada hóspede.</p></div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
