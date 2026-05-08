<?php
$pageTitle = 'Depoimentos';
$pageDesc  = 'Relatos de hóspedes da Pousada Aromas da Serra sobre estadia, gastronomia, acolhimento e reconexão.';
$pageSlug  = 'depoimentos';
require __DIR__ . '/includes/header.php';

$fallbackTestimonials = [
    ['author'=>'Maíra Almeida','context'=>'Estadia na pousada','rating'=>5,'quote'=>'Ficamos muito felizes com a nossa estadia na pousada. Tudo feito com muito bom gosto e carinho, atendimento impecável, acomodação excelente e uma culinária maravilhosa. A proposta social e ambiental da pousada também é um ponto de destaque. Recomendamos.'],
    ['author'=>'Fabíolla Mello','context'=>'Hospedagem e gastronomia','rating'=>5,'quote'=>'Uma hospedagem surpreendente. Esperávamos uma boa experiência, mas a pousada, capitaneada por Jürg e Cristina, arrebatou nossos corações pela acolhida amorosa, comida delicada e saborosa, conversas animadas, paz, sossego e uma paisagem exuberante. Uma combinação perfeita para acalmar corpo e mente e voltar já com saudade. Também destaco a receptividade carinhosa da ágil Andressa. Super recomendo.'],
    ['author'=>'Marina Fiuza','context'=>'Experiência completa','rating'=>5,'quote'=>'A experiência como um todo foi uma grata surpresa: os cuidados conosco, a gastronomia, o chá e a fogueira. Obrigada pela recepção e disponibilidade.'],
];
$testimonials = catalog_testimonials($fallbackTestimonials);
?>
<section class="page-hero">
  <img class="page-hero-img kenburns" src="<?= e(repair_image_url(block('depoimentos','hero_image','https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=2000&q=80'))) ?>" alt="Depoimentos de hóspedes">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('depoimentos','hero_eyebrow','Depoimentos')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] text-reveal"><?= block('depoimentos','hero_title','Histórias que ficam <em class="serif-italic text-gold-500">na memória.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg reveal"><?= block('depoimentos','hero_subtitle','Relatos de quem viveu a pousada com calma, afeto, gastronomia e reconexão.') ?></p>
  </div>
</section>

<section class="section bg-cream-50 paper">
  <div class="max-w-7xl mx-auto px-6">
    <div class="reveal max-w-3xl">
      <span class="eyebrow"><?= e(block('depoimentos','intro_eyebrow','Vozes dos hóspedes')) ?></span>
      <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-3 leading-tight"><?= block('depoimentos','intro_title','A experiência contada por <em class="serif-italic text-terracota-500">quem esteve aqui.</em>') ?></h2>
    </div>

    <div class="mt-12 grid lg:grid-cols-3 gap-6 reveal-stagger">
      <?php foreach ($testimonials as $item): ?>
        <?php $rating = max(1, min(5, (int)($item['rating'] ?? 5))); ?>
        <article class="card-elevated p-7 flex flex-col min-h-[360px]">
          <div class="flex items-center justify-between gap-4">
            <i data-lucide="quote" class="w-9 h-9 text-gold-600"></i>
            <div class="flex gap-1 text-gold-600" aria-label="<?= $rating ?> de 5 estrelas">
              <?php for ($i=0; $i<$rating; $i++): ?><i data-lucide="star" class="w-4 h-4 fill-current"></i><?php endfor; ?>
            </div>
          </div>
          <p class="mt-7 text-[16px] leading-[1.85] text-ink-700/85 flex-1"><?= nl2br(e((string)$item['quote'])) ?></p>
          <footer class="mt-8 pt-5 border-t border-cream-200">
            <strong class="font-editorial text-2xl text-forest-900 font-normal"><?= e((string)$item['author']) ?></strong>
            <?php if (!empty($item['context'])): ?><span class="block mt-1 text-[11px] tracking-eyebrow uppercase text-terracota-500"><?= e((string)$item['context']) ?></span><?php endif; ?>
          </footer>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>