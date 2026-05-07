<?php
$pageTitle = 'Refúgio na Suíça Alagoana';
$pageDesc  = 'Pousada Aromas da Serra — refúgio boutique em Mar Vermelho (AL). Gastronomia mediterrânea, contemplação e ritual da fogueira no coração da serra.';
$pageSlug  = 'home';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/partials.php';
?>

<!-- ============ HERO ============ -->
<section class="relative min-h-[94vh] grid items-end overflow-hidden">
  <picture class="absolute inset-0 kenburns">
    <source media="(max-width:640px)" srcset="<?= e(block('home','hero_image','https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=900&q=70')) ?>">
    <img src="<?= e(block('home','hero_image','https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=2000&q=80')) ?>"
         alt="Vista da serra alagoana ao amanhecer" class="w-full h-full object-cover">
  </picture>
  <div class="absolute inset-0 bg-tint"></div>

  <div class="absolute inset-x-0 top-24 text-center pointer-events-none">
    <span class="text-cream-50/85 tracking-eyebrow uppercase text-[11px]">Pousada · Mar Vermelho — Alagoas</span>
  </div>

  <div class="relative max-w-6xl mx-auto px-6 pb-24 md:pb-32 text-cream-50">
    <p class="font-editorial text-cream-50/85 text-lg mb-4"><?= block('home','hero_eyebrow','— Seja bem-vindo —') ?></p>
    <h1 class="font-editorial text-5xl md:text-7xl lg:text-[88px] leading-[1.02] tracking-tight max-w-5xl text-reveal">
      <?= block('home','hero_title','Onde o silêncio da serra<br><em class="serif-italic text-gold-500">acolhe e transforma.</em>') ?>
    </h1>
    <p class="mt-6 max-w-xl text-cream-100/90 text-lg leading-relaxed reveal">
      <?= block('home','hero_subtitle','Um refúgio exclusivo para adultos em meio à <strong class="text-gold-500/95 font-medium">Suíça Alagoana</strong> — gastronomia mediterrânea, contemplação e tempo para si.') ?>
    </p>
    <div class="mt-9 flex flex-wrap gap-3 reveal">
      <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-gold magnetic"><i data-lucide="calendar-heart" class="w-4 h-4"></i> Reservar estadia</a>
      <a href="<?= url('a-pousada.php') ?>" class="btn-ghost-light">Conheça a pousada <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
    </div>
  </div>

  <a href="#manifesto" class="scroll-cue" aria-label="Descer para o conteúdo">
    <span class="scroll-cue__label">Descer</span>
    <span class="scroll-cue__mouse"></span>
    <span class="scroll-cue__line"></span>
  </a>
</section>

<!-- ============ MANIFESTO ============ -->
<section id="manifesto" class="section bg-cream-50">
  <div class="max-w-5xl mx-auto px-6 text-center reveal">
    <span class="eyebrow"><?= e(block('home','manifesto_eyebrow','Sobre a pousada')) ?></span>
    <h2 class="font-editorial text-4xl md:text-6xl text-forest-900 mt-5 leading-[1.05]"><?= block('home','manifesto_title','Um destino para quem busca <em class="serif-italic text-terracota-500">tranquilidade e conforto.</em>') ?></h2>
    <p class="mt-7 text-[18px] leading-[1.85] text-ink-700/90 max-w-3xl mx-auto">
      <?= block('home','manifesto_body','Localizada entre as serras de Alagoas, com culinária sofisticada de influência europeia, a Aromas da Serra oferece uma viagem de <em class="serif-italic">sabores, cores e bem-estar</em>. Aqui, o tempo desacelera no encontro com a natureza, com vinhos especiais e com receitas que viajam pela Suíça, sul da França, Itália e Mediterrâneo.') ?>
    </p>
    <p class="mt-5 text-[16px] text-ink-700/70 max-w-2xl mx-auto">
      <?= e(block('home','manifesto_note','Exclusivamente para adultos. Sem TVs nos quartos. Sem pressa. Apenas presença.')) ?>
    </p>
    <div class="mt-10"><div class="divider-gold"></div></div>
  </div>
</section>

<!-- ============ DESTAQUES ============ -->
<section class="section bg-forest-900 text-cream-100 relative overflow-hidden noise">
  <div class="absolute inset-0 opacity-[0.06] bg-[radial-gradient(circle_at_70%_30%,#c4a46c_0,transparent_55%)]"></div>
  <div class="relative max-w-7xl mx-auto px-6">
    <header class="text-center max-w-2xl mx-auto reveal">
      <span class="eyebrow text-gold-500/80">Diferenciais</span>
      <h2 class="font-editorial text-4xl md:text-5xl text-cream-50 mt-3">O que nos faz <em class="serif-italic text-gold-500">únicos.</em></h2>
    </header>
    <div class="mt-14 grid md:grid-cols-3 gap-10 reveal-stagger">
      <article class="text-center">
        <i data-lucide="utensils-crossed" class="w-9 h-9 mx-auto text-gold-500"></i>
        <h3 class="font-editorial text-2xl text-cream-50 mt-5">Cozinha Mediterrânea</h3>
        <p class="mt-3 text-cream-100/75 leading-relaxed">Receitas autorais inspiradas no sul da França, Itália e Mediterrâneo, harmonizadas com vinhos especiais.</p>
      </article>
      <article class="text-center">
        <i data-lucide="flame" class="w-9 h-9 mx-auto text-gold-500"></i>
        <h3 class="font-editorial text-2xl text-cream-50 mt-5">Ritual da Fogueira</h3>
        <p class="mt-3 text-cream-100/75 leading-relaxed">Noites de celebração à beira do fogo — música, conversas e gastronomia em um ambiente íntimo e ancestral.</p>
      </article>
      <article class="text-center">
        <i data-lucide="leaf" class="w-9 h-9 mx-auto text-gold-500"></i>
        <h3 class="font-editorial text-2xl text-cream-50 mt-5">Contemplação</h3>
        <p class="mt-3 text-cream-100/75 leading-relaxed">Espaços de leitura, redário, mandala e o caminho das pedras — feitos para o reencontro consigo.</p>
      </article>
    </div>
  </div>
</section>

<!-- ============ CHALÉS ============ -->
<section class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6">
    <header class="max-w-3xl reveal">
      <span class="eyebrow">Acomodações</span>
      <h2 class="font-editorial text-4xl md:text-6xl text-forest-900 mt-4 leading-tight">Chalés que <em class="serif-italic text-terracota-500">abraçam o jardim.</em></h2>
      <p class="mt-5 text-[17px] leading-[1.85] text-ink-700/85">Cada detalhe foi pensado para proporcionar tranquilidade — aconchego, conforto, gentilezas e o contato constante com a natureza.</p>
    </header>

    <div class="mt-14 grid md:grid-cols-3 gap-7 reveal-stagger">
      <?php
      $cards = [
        ['lavanda', 'Chalé Lavanda', 'Luxo · Vista panorâmica', 'Único com vista panorâmica para a serra. Varanda privativa com rede e decoração charmosa.', [
          'photo-1582719478250-c89cae4dc85b','photo-1540541338287-41700207dee6','photo-1598300042247-d088f8ab3a91',
        ]],
        ['manjericao', 'Chalé Manjericão', 'Luxo VIP · Vista jardim', 'Refúgio sofisticado para retiros sabáticos — estrutura completa para permanências mais prolongadas.', [
          'photo-1505691938895-1758d7feb511','photo-1505693416388-ac5ce068fe85','photo-1567016432779-094069958ea5',
        ]],
        ['standard', 'Chalés Aromáticos', 'Standard · Vista jardim', 'Alecrim, Capim Cidreira, Calêndula, Erva Doce, Melissa e Jasmim — vista para o nosso lindo e perfumado jardim.', [
          'photo-1566073771259-6a8506099945','photo-1611892440504-42a792e24d32','photo-1591088398332-8a7791972843',
        ]],
      ];
      foreach($cards as [$slug,$title,$tag,$desc,$ph]):
        $slides = array_map(fn($p) => ['src' => "https://images.unsplash.com/$p?auto=format&fit=crop&w=900&q=80", 'alt' => $title], $ph);
      ?>
        <article class="card-elevated overflow-hidden flex flex-col group">
          <div class="relative">
            <?php embla_carousel($slides, ['ratio'=>'4/5','autoplay'=>true,'arrows'=>true,'dots'=>true,'lightbox'=>false]); ?>
            <span class="absolute top-4 left-4 z-[2] bg-cream-50/95 text-forest-900 text-[10px] tracking-eyebrow uppercase px-3 py-1 rounded-full"><?= e($tag) ?></span>
          </div>
          <div class="p-7 flex flex-col flex-1">
            <h3 class="font-editorial text-2xl text-forest-900"><?= e($title) ?></h3>
            <p class="mt-2 text-ink-700/80 leading-relaxed text-[15px] flex-1"><?= e($desc) ?></p>
            <a href="<?= url('chales.php#' . $slug) ?>" class="mt-5 inline-flex items-center gap-1 text-[12px] tracking-eyebrow uppercase text-terracota-500 hover:text-terracota-600 transition w-fit">Conhecer <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="mt-12 text-center"><a href="<?= url('chales.php') ?>" class="btn-ghost magnetic">Todas as acomodações</a></div>
  </div>
</section>

<!-- ============ GASTRONOMIA ============ -->
<section class="section bg-cream-100 paper relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
    <div class="reveal order-2 lg:order-1">
      <span class="eyebrow">Experiência gastronômica</span>
      <h2 class="font-editorial text-4xl md:text-6xl text-forest-900 mt-4 leading-[1.05]"><?= block('home','gastro_title','Cozinha <em class="serif-italic text-terracota-500">Mediterrânea.</em>') ?></h2>
      <p class="mt-6 text-[17px] leading-[1.9] text-ink-700/90">
        <?= block('home','gastro_body','As receitas da Pousada Aromas da Serra são fruto de uma jornada culinária pela Suíça, sul da França, Itália e Mediterrâneo. Combinando ingredientes frescos e temperos colhidos diretamente da nossa <em class="serif-italic">Mandala</em>, cada prato é uma alquimia de sabores e aromas.') ?>
      </p>
      <p class="mt-4 text-[17px] leading-[1.9] text-ink-700/90">
        Mesa farta, tempo desacelerado e harmonização com vinhos muito especiais. A gastronomia é o coração da experiência, não um adicional.
      </p>
      <ul class="mt-7 grid sm:grid-cols-2 gap-3 text-[15px]">
        <li class="flex items-start gap-2"><i data-lucide="wheat" class="w-4 h-4 mt-1 text-gold-600"></i> Pães rústicos artesanais</li>
        <li class="flex items-start gap-2"><i data-lucide="wine" class="w-4 h-4 mt-1 text-gold-600"></i> Carta de vinhos curada</li>
        <li class="flex items-start gap-2"><i data-lucide="flame" class="w-4 h-4 mt-1 text-gold-600"></i> Temporada de fondues</li>
        <li class="flex items-start gap-2"><i data-lucide="sprout" class="w-4 h-4 mt-1 text-gold-600"></i> Ervas da nossa Mandala</li>
      </ul>
      <div class="mt-9 flex flex-wrap gap-3">
        <a href="<?= url('gastronomia.php') ?>" class="btn-primary magnetic"><i data-lucide="utensils-crossed" class="w-4 h-4"></i> Descobrir sabores</a>
        <a href="<?= url('taberna.php') ?>" class="btn-ghost">Taberna do Monge</a>
      </div>
    </div>
    <div class="order-1 lg:order-2 reveal">
      <div class="rounded-md overflow-hidden shadow-2xl">
        <?php embla_carousel([
          ['src'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1100&q=80', 'alt'=>'Mesa mediterrânea'],
          ['src'=>'https://images.unsplash.com/photo-1559717865-a99cac1c95d8?auto=format&fit=crop&w=1100&q=80', 'alt'=>'Fondue'],
          ['src'=>'https://images.unsplash.com/photo-1473093226795-af9932fe5856?auto=format&fit=crop&w=1100&q=80', 'alt'=>'Prato'],
          ['src'=>'https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&w=1100&q=80', 'alt'=>'Massa'],
        ], ['ratio'=>'4/5','autoplay'=>true]); ?>
      </div>
    </div>
  </div>
</section>

<!-- ============ EXPERIÊNCIAS ============ -->
<section class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6">
    <header class="text-center max-w-3xl mx-auto reveal">
      <span class="eyebrow">Vivências na Serra</span>
      <h2 class="font-editorial text-4xl md:text-6xl text-forest-900 mt-4 leading-[1.05]">Rituais que <em class="serif-italic text-terracota-500">tocam a alma.</em></h2>
      <p class="gallery-mobile-hint"><i data-lucide="maximize-2" class="w-4 h-4"></i> Toque nas fotos para abrir a galeria em tela cheia.</p>
    </header>

    <div class="mt-14 grid md:grid-cols-2 lg:grid-cols-4 gap-5 reveal-stagger">
      <?php
      $tiles = [
        ['photo-1517248135467-4c7edcad34c4','Ritual do Chá da Tarde'],
        ['photo-1542367592-8849eb950fd8','Ritual da Fogueira'],
        ['photo-1466692476868-aef1dfb1e735','Mandala · horta orgânica'],
        ['photo-1522098635833-216c03d20ad4','Espaço Redário'],
      ]; foreach($tiles as [$ph,$cap]): $src="https://images.unsplash.com/$ph?auto=format&fit=crop&w=900&q=80"; ?>
        <a href="<?= e($src) ?>" class="glightbox gallery-tile aspect-[3/4] block" data-gallery="home-exp" data-type="image" data-description="<?= e($cap) ?>">
          <img src="<?= e($src) ?>" alt="<?= e($cap) ?>" class="w-full h-full object-cover" loading="lazy">
          <span class="gallery-action"><i data-lucide="maximize-2" class="w-3.5 h-3.5"></i> Abrir galeria</span>
          <figcaption><?= e($cap) ?></figcaption>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="mt-12 text-center"><a href="<?= url('experiencias.php') ?>" class="btn-ghost magnetic">Todas as experiências</a></div>
  </div>
</section>

<!-- ============ ITINERÁRIO TEASER ============ -->
<section class="section bg-cream-100 paper relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
    <div class="reveal">
      <img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1100&q=80" alt="Estrada para Mar Vermelho" class="w-full aspect-[5/4] object-cover rounded-md shadow-xl">
    </div>
    <div class="reveal">
      <span class="eyebrow">Itinerário</span>
      <h2 class="font-editorial text-4xl md:text-6xl text-forest-900 mt-4 leading-[1.05]">A viagem até a serra <em class="serif-italic text-terracota-500">já é parte da experiência.</em></h2>
      <p class="mt-6 text-[17px] leading-[1.9] text-ink-700/85">
        Preparamos um roteiro afetuoso pelo trajeto até <strong>Mar Vermelho</strong>: paradas gastronômicas, artesanato local, cafés e experiências culturais que tornam o caminho tão especial quanto o destino.
      </p>
      <a href="<?= url('itinerario.php') ?>" class="btn-primary magnetic mt-8"><i data-lucide="map" class="w-4 h-4"></i> Ver itinerário completo</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
