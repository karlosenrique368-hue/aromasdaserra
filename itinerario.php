<?php
$pageTitle='Itinerário até a Serra'; $pageDesc='Roteiro aconchegante pela serra, com paradas em Capela, Cajueiro e Viçosa no caminho até Mar Vermelho.'; $pageSlug='itinerario';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/partials.php';
?>
<section class="page-hero">
  <img class="page-hero-img" src="<?= e(block('itinerario','hero_image','https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=2000&q=80')) ?>" alt="Estrada de serra">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('itinerario','hero_eyebrow','Roteiro aconchegante')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05]"><?= block('itinerario','hero_title','Roteiro aconchegante <em class="serif-italic text-gold-500">pela serra.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg"><?= block('itinerario','hero_subtitle','Da saída de Maceió à chegada em Mar Vermelho, o caminho também acolhe.') ?></p>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-4xl mx-auto px-6 reveal text-center">
    <span class="eyebrow"><?= e(block('itinerario','intro_eyebrow','Roteiro aconchegante')) ?></span>
    <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-4 leading-tight"><?= block('itinerario','intro_title','Paradas especiais <em class="serif-italic text-terracota-500">pela serra.</em>') ?></h2>
    <p class="mt-6 text-[17px] leading-[1.9] text-ink-700/85">
      <?= block('itinerario','intro_body','O caminho até a pousada parte de Maceió e segue por Capela, Cajueiro e Viçosa antes da chegada a Mar Vermelho. A proposta é aproveitar o percurso com calma, fazendo pausas especiais indicadas pela Aromas da Serra.') ?>
    </p>
  </div>

  <div class="max-w-4xl mx-auto px-6 mt-16 space-y-12">
    <?php $stops = [];
    $defaultStopImages = [
      'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=800&q=80',
      'https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=800&q=80',
      'https://images.unsplash.com/photo-1485921325833-c519f76c4927?auto=format&fit=crop&w=800&q=80',
      'https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=800&q=80',
    ];
    for ($i = 1; $i <= 4; $i++) {
      $title = block('itinerario', "stop_{$i}_title", ['Capela · Artesanato e Caldinho','Cajueiro · Cerâmica Caju Queimado','Viçosa · Padaria do Creso','Mar Vermelho · Chegada à Pousada'][$i - 1]);
      $stops[] = [
        (string)$i,
        $title,
        block('itinerario', "stop_{$i}_body", ['Ao chegar em Capela, façam duas paradas especiais: o artesanato do Sr. João de Barro e o tradicional Caldinho de Capela, que funciona até às 12h, quase uma pausa obrigatória para quem passa por lá.','Seguindo para Cajueiro, aproveitem a Cerâmica Caju Queimado, a entrada é sinalizada.','Chegando em Viçosa, façam uma pausa na Padaria do Creso para um café acolhedor.','Depois, sigam para Mar Vermelho para viver o descanso e aconchego conosco.'][$i - 1]),
        block('itinerario', "stop_{$i}_icon", ['hand-heart','palette','coffee','home'][$i - 1]),
        gallery_slides(block('itinerario', "stop_{$i}_image", $defaultStopImages[$i - 1]), $title),
      ];
    }
    foreach($stops as [$n,$t,$d,$ic,$slides]): ?>
      <article class="reveal grid md:grid-cols-[auto,1fr,1fr] gap-6 items-center">
        <div class="step-num"><?= e($n) ?></div>
        <div>
          <span class="inline-flex items-center gap-2 text-[11px] tracking-eyebrow uppercase text-terracota-500"><i data-lucide="<?= $ic ?>" class="w-3.5 h-3.5"></i> Parada <?= e($n) ?></span>
          <h3 class="font-editorial text-3xl text-forest-900 mt-2 leading-tight"><?= e($t) ?></h3>
          <p class="mt-3 text-[16px] leading-[1.8] text-ink-700/85"><?= $d ?></p>
        </div>
        <div class="rounded-md overflow-hidden shadow-lg">
          <?php embla_carousel($slides, ['ratio'=>'4/3','autoplay'=>false,'lightbox'=>true,'group'=>'itinerario-stop-' . $n]); ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <div class="mt-20 text-center reveal">
    <p class="serif-italic text-forest-800 text-2xl max-w-2xl mx-auto"><?= block('itinerario','quote','"Qualquer orientação, estamos à disposição."') ?></p>
    <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-primary mt-9"><i data-lucide="calendar-heart" class="w-4 h-4"></i> Planejar minha viagem</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
