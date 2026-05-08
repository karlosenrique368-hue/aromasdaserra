<?php
$pageTitle='Itinerário até a Serra'; $pageDesc='Roteiro afetivo de paradas, sabores e cultura no caminho até Mar Vermelho — a viagem como parte da experiência.'; $pageSlug='itinerario';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
  <img class="page-hero-img" src="<?= e(block('itinerario','hero_image','https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=2000&q=80')) ?>" alt="Estrada de serra">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('itinerario','hero_eyebrow','A viagem')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05]"><?= block('itinerario','hero_title','Itinerário <em class="serif-italic text-gold-500">até Mar Vermelho.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg"><?= block('itinerario','hero_subtitle','O trajeto até a serra é parte da hospedagem.') ?></p>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-4xl mx-auto px-6 reveal text-center">
    <span class="eyebrow"><?= e(block('itinerario','intro_eyebrow','Roteiro afetivo')) ?></span>
    <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-4 leading-tight"><?= block('itinerario','intro_title','Sabores, ofícios e <em class="serif-italic text-terracota-500">paisagens</em> pelo caminho.') ?></h2>
    <p class="mt-6 text-[17px] leading-[1.9] text-ink-700/85">
      <?= block('itinerario','intro_body','Saindo de Maceió, o trajeto até Mar Vermelho cruza paisagens que mudam — do litoral à serra, da agricultura à cultura popular. Sugerimos paradas que tornam a viagem inesquecível.') ?>
    </p>
  </div>

  <div class="max-w-4xl mx-auto px-6 mt-16 space-y-12">
    <?php $stops = [];
    for ($i = 1; $i <= 6; $i++) {
      $stops[] = [
        (string)$i,
        block('itinerario', "stop_{$i}_title", ['Saída de Maceió','Marechal Deodoro · Centro Histórico','Atalaia · Casa de Doces e Conservas','Quebrangulo · Mata Atlântica','Palmeira dos Índios · Cultura e Memória','Mar Vermelho · Chegada à Pousada'][$i - 1]),
        block('itinerario', "stop_{$i}_body", ['Café da manhã reforçado antes da estrada — um bom ponto: padarias do bairro Jatiúca ou Ponta Verde.','Igrejas barrocas, artesanato em palha e doces caseiros. Vale uma pausa de 30 a 45 minutos.','Doces de buriti, geleias artesanais e queijos da região — leve algumas para a estadia.','Mirante natural e vegetação preservada. Ideal para esticar as pernas e respirar fundo.','Visite o Museu Xucurus — história indígena e cultura local que enriquecem a viagem.','Bem-vindo à Suíça Alagoana. Um chá quente espera por você na Taberna do Monge.'][$i - 1]),
        block('itinerario', "stop_{$i}_icon", ['coffee','landmark','candy','trees','book-open','home'][$i - 1]),
        repair_image_url(block('itinerario', "stop_{$i}_image", ['https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1583394838336-acd977736f90?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1485921325833-c519f76c4927?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1547981609-4b6bfe67ca0b?auto=format&fit=crop&w=800&q=80','https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=800&q=80'][$i - 1])),
      ];
    }
    foreach($stops as [$n,$t,$d,$ic,$img]): ?>
      <article class="reveal grid md:grid-cols-[auto,1fr,1fr] gap-6 items-center">
        <div class="step-num"><?= e($n) ?></div>
        <div>
          <span class="inline-flex items-center gap-2 text-[11px] tracking-eyebrow uppercase text-terracota-500"><i data-lucide="<?= $ic ?>" class="w-3.5 h-3.5"></i> Parada <?= e($n) ?></span>
          <h3 class="font-editorial text-3xl text-forest-900 mt-2 leading-tight"><?= e($t) ?></h3>
          <p class="mt-3 text-[16px] leading-[1.8] text-ink-700/85"><?= $d ?></p>
        </div>
        <img src="<?= e($img) ?>" alt="<?= e($t) ?>" class="rounded-md aspect-[4/3] object-cover w-full shadow-lg">
      </article>
    <?php endforeach; ?>
  </div>

  <div class="mt-20 text-center reveal">
    <p class="serif-italic text-forest-800 text-2xl max-w-2xl mx-auto"><?= block('itinerario','quote','"Transformamos o trajeto em parte da experiência — porque o caminho desacelera o coração antes mesmo da serra."') ?></p>
    <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-primary mt-9"><i data-lucide="calendar-heart" class="w-4 h-4"></i> Planejar minha viagem</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
