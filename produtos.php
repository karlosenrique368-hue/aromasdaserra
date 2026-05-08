<?php
$pageTitle = 'Produtos Artesanais';
$pageDesc  = 'Mostruário de produtos artesanais da Pousada Aromas da Serra: geleias, pães, temperos e delicadezas da casa.';
$pageSlug  = 'produtos';
require __DIR__ . '/includes/header.php';

$fallbackProducts = [
    ['title'=>'Geleias Especiais','category'=>'Geleias artesanais','description'=>'Geleias preparadas em pequenos lotes, com frutas selecionadas e combinações que transitam entre o doce, o cítrico e o levemente picante.','flavors'=>"Jaboticaba\nAcerola com hibisco\nLaranja\nAmora\nManga com maracujá, cachaça e pimenta",'cover'=>'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?auto=format&fit=crop&w=1000&q=85'],
    ['title'=>'Pães Artesanais','category'=>'Pães da casa','description'=>'Pães de fermentação cuidadosa, pensados para acompanhar cafés, tábuas, entradas e momentos de partilha à mesa.','flavors'=>"Pão de ervas frescas\nPão sem glúten\nPão de multigrãos\nPão de azeitona\nPão ciabatta",'cover'=>'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=1000&q=85'],
    ['title'=>'Vinagre Aromatizado','category'=>'Temperos autorais','description'=>'Vinagre aromático para finalizar saladas, legumes e preparos especiais com um toque fresco da casa.','flavors'=>'Mostruário sujeito à disponibilidade da produção artesanal.','cover'=>'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=1000&q=85'],
    ['title'=>'Sal de Sálvia e Laranja','category'=>'Temperos autorais','description'=>'Sal aromatizado com sálvia e notas cítricas de laranja, ideal para realçar carnes, legumes, pães e finalizações.','flavors'=>'Sálvia e laranja','cover'=>'https://images.unsplash.com/photo-1506368249639-73a05d6f6488?auto=format&fit=crop&w=1000&q=85'],
    ['title'=>'Biscoito Quero-Quero','category'=>'Delicadezas da casa','description'=>'Biscoito artesanal para acompanhar cafés, chás e pausas doces durante a estadia.','flavors'=>'Receita da casa','cover'=>'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=1000&q=85'],
];
$products = catalog_products($fallbackProducts);
?>
<section class="page-hero">
  <img class="page-hero-img kenburns" src="<?= e(repair_image_url(block('produtos','hero_image','https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=2000&q=80'))) ?>" alt="Produtos artesanais">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('produtos','hero_eyebrow','Mostruário artesanal')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] text-reveal"><?= block('produtos','hero_title','Produtos que levam <em class="serif-italic text-gold-500">aromas para casa.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg reveal"><?= block('produtos','hero_subtitle','Geleias, pães, temperos e delicadezas produzidas em pequenos lotes, apenas como catálogo de apresentação.') ?></p>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6">
    <div class="reveal max-w-3xl">
      <span class="eyebrow"><?= e(block('produtos','intro_eyebrow','Mostruário da casa')) ?></span>
      <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-3 leading-tight"><?= block('produtos','intro_title','Pequenos lotes, <em class="serif-italic text-terracota-500">muito cuidado.</em>') ?></h2>
      <p class="mt-5 text-[17px] leading-[1.85] text-ink-700/85"><?= block('produtos','intro_body','Os produtos artesanais da Aromas da Serra nascem da mesma cozinha afetiva que acolhe nossos hóspedes: ingredientes selecionados, ervas aromáticas e receitas preparadas com tempo. Consulte disponibilidade durante a sua estadia ou pelo WhatsApp.') ?></p>
    </div>

    <div class="mt-12 grid md:grid-cols-2 xl:grid-cols-3 gap-6 reveal-stagger">
      <?php foreach ($products as $product): ?>
        <?php $flavors = product_flavor_items((string)($product['flavors'] ?? '')); ?>
        <article class="card-elevated overflow-hidden flex flex-col">
          <div class="relative aspect-[4/3] overflow-hidden bg-cream-100">
            <?php if (!empty($product['cover'])): ?><img src="<?= e(repair_image_url((string)$product['cover'])) ?>" alt="<?= e((string)$product['title']) ?>" class="w-full h-full object-cover transition duration-700 hover:scale-105" loading="lazy"><?php endif; ?>
            <span class="absolute top-4 left-4 bg-cream-50/95 text-terracota-500 text-[10px] tracking-eyebrow uppercase rounded-full px-3 py-1 shadow-sm"><?= e((string)($product['category'] ?? 'Produto artesanal')) ?></span>
          </div>
          <div class="p-7 flex flex-col flex-1">
            <h3 class="font-editorial text-3xl text-forest-900 leading-tight"><?= e((string)$product['title']) ?></h3>
            <p class="mt-3 text-[15px] text-ink-700/80 leading-relaxed"><?= nl2br(e((string)($product['description'] ?? ''))) ?></p>
            <?php if ($flavors): ?>
              <div class="mt-6 pt-5 border-t border-cream-200">
                <span class="text-[10px] tracking-eyebrow uppercase text-gold-600">Sabores disponíveis</span>
                <ul class="mt-3 grid gap-2 text-[14px] text-ink-700/85">
                  <?php foreach ($flavors as $flavor): ?><li class="flex gap-2"><i data-lucide="leaf" class="w-4 h-4 text-terracota-500 shrink-0 mt-0.5"></i><span><?= e($flavor) ?></span></li><?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-primary magnetic mt-7 self-start"><i data-lucide="message-circle" class="w-4 h-4"></i> Consultar</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>