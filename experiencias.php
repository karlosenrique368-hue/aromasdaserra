<?php
$pageTitle='Experiências'; $pageDesc='Rituais e vivências exclusivas: Fogueira, Chá da Tarde, Mandala e Caminho das Pedras.'; $pageSlug='experiencias';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/partials.php';
?>
<section class="page-hero">
  <img class="page-hero-img kenburns" src="<?= e(block('experiencias','hero_image','https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=2000&q=80')) ?>" alt="Experiências">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('experiencias','hero_eyebrow','Vivências na Serra')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] text-reveal"><?= block('experiencias','hero_title','Rituais que <em class="serif-italic text-gold-500">tocam a alma.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg reveal"><?= block('experiencias','hero_subtitle','Experiências autorais — desenhadas para o reencontro com o tempo, com a natureza e com você mesmo.') ?></p>
  </div>
</section>

<!-- DESTAQUES -->
<section class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6">
    <header class="reveal max-w-3xl">
      <span class="eyebrow">Os 4 rituais</span>
      <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-3 leading-tight">Cada experiência, um <em class="serif-italic text-terracota-500">presente.</em></h2>
      <p class="gallery-mobile-hint !justify-start"><i data-lucide="maximize-2" class="w-4 h-4"></i> Nas fotos, toque para ampliar e navegar pelo lightbox.</p>
    </header>

    <div class="mt-12 grid lg:grid-cols-2 gap-8 reveal-stagger">
      <?php
      $exps = [
        ['Ritual da Fogueira','Encontros à beira do fogo — música, conversas e gastronomia em ambiente íntimo e ancestral.', 'flame', [
          'photo-1542367592-8849eb950fd8','photo-1455218873509-8097305ee378','photo-1474482546248-690a01702af3',
        ]],
        ['Ritual do Chá da Tarde','Bolos artesanais, pães rústicos e chás especiais ao final do dia — em frente à serra.', 'coffee', [
          'photo-1517248135467-4c7edcad34c4','photo-1576092768241-dec231879fc3','photo-1545665277-5937489579f2',
        ]],
        ['Mandala — horta orgânica','Ervas, flores comestíveis e temperos colhidos diretamente para a sua mesa.', 'sprout', [
          'photo-1466692476868-aef1dfb1e735','photo-1416879595882-3373a0480b5b','photo-1492496913980-501348b61469',
        ]],
        ['Caminho das Pedras','Trajeto contemplativo pelo bosque da pousada — silêncio, presença e a serra à vista.', 'mountain', [
          'photo-1469474968028-56623f02e42e','photo-1518770660439-4636190af475','photo-1473773508845-188df298d2d1',
        ]],
      ];
      foreach($exps as [$t,$d,$ic,$ph]):
        $slides = array_map(fn($p) => ['src'=>"https://images.unsplash.com/$p?auto=format&fit=crop&w=1200&q=85", 'alt'=>$t], $ph);
      ?>
        <article class="card-elevated overflow-hidden">
          <div class="relative">
            <?php embla_carousel($slides, ['ratio'=>'16/10','autoplay'=>true,'lightbox'=>true,'group'=>strtolower(strtr($t,[' '=>'-','—'=>'']))]); ?>
            <span class="absolute top-4 right-4 z-[2] bg-cream-50 text-terracota-500 w-11 h-11 grid place-items-center rounded-full shadow-md"><i data-lucide="<?= $ic ?>" class="w-5 h-5"></i></span>
          </div>
          <div class="p-7">
            <h3 class="font-editorial text-2xl text-forest-900"><?= e($t) ?></h3>
            <p class="mt-2 text-[15px] text-ink-700/80 leading-relaxed"><?= e($d) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- BENEFÍCIOS -->
<section class="section bg-forest-900 text-cream-100 noise relative overflow-hidden">
  <div class="absolute inset-0 opacity-[0.06] bg-[radial-gradient(circle_at_30%_70%,#c4a46c_0,transparent_55%)]"></div>
  <div class="relative max-w-6xl mx-auto px-6 text-center">
    <span class="eyebrow text-gold-500/80">Bem-estar incluso</span>
    <h2 class="font-editorial text-4xl md:text-5xl text-cream-50 mt-3">Cuidado em cada <em class="serif-italic text-gold-500">detalhe.</em></h2>
    <div class="mt-12 grid md:grid-cols-3 gap-10 reveal-stagger">
      <div><i data-lucide="leaf" class="w-9 h-9 mx-auto text-gold-500"></i><h3 class="font-editorial text-2xl text-cream-50 mt-4">Espaço Redário</h3><p class="text-cream-100/75 mt-2">Para a leitura e a contemplação no jardim aromático.</p></div>
      <div><i data-lucide="book-open" class="w-9 h-9 mx-auto text-gold-500"></i><h3 class="font-editorial text-2xl text-cream-50 mt-4">Biblioteca</h3><p class="text-cream-100/75 mt-2">Curadoria de livros para acompanhar o silêncio da serra.</p></div>
      <div><i data-lucide="wine" class="w-9 h-9 mx-auto text-gold-500"></i><h3 class="font-editorial text-2xl text-cream-50 mt-4">Carta de Vinhos</h3><p class="text-cream-100/75 mt-2">Rótulos selecionados para harmonizar cada momento.</p></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
