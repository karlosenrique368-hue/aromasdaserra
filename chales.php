<?php
$pageTitle = 'Chalés';
$pageDesc  = 'Chalés Lavanda, Manjericão e Aromáticos, hospedagem exclusiva em Mar Vermelho, AL.';
$pageSlug  = 'chales';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/partials.php';

function chalet_slug_id(string $text): string {
    return preg_replace('/[^a-z0-9]+/', '-', strtolower(strtr($text, ['ç'=>'c','ã'=>'a','á'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u'])));
}

function chalet_view_slides(array $row): array {
    $slides = gallery_slides((string)($row['gallery'] ?? ''), (string)($row['name'] ?? 'Chalé'));
    if (!$slides && !empty($row['cover'])) $slides[] = ['src' => repair_image_url((string)$row['cover']), 'alt' => (string)$row['name']];
    return $slides;
}

function chalet_video_button(array $row, bool $dark, string $extraClass = ''): string {
  $embed = youtube_embed_url((string)($row['video_url'] ?? ''));
  if ($embed === '') return '';
  $label = trim((string)($row['video_label'] ?? '')) ?: 'Ver vídeo do chalé';
  $class = $dark ? 'btn-ghost-light' : 'btn-ghost';
  $extraClass = trim($extraClass);
  return '<a href="' . e($embed) . '" class="glightbox ' . $class . ' magnetic' . ($extraClass !== '' ? ' ' . e($extraClass) : '') . '" data-type="video" data-width="900" data-height="506"><i data-lucide="play" class="w-4 h-4"></i> ' . e($label) . '</a>';
}

$fallbackChalets = [
    [
        'slug'=>'lavanda', 'name'=>'Chalé Lavanda', 'category'=>'Luxo · Vista panorâmica', 'view'=>'Vista panorâmica para a serra',
        'description'=>'Único com vista panorâmica para a serra. Varanda privativa com rede, decoração charmosa e conforto para uma hospedagem inesquecível.',
        'cover'=>'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85',
        'gallery'=>implode("\n", ['https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85','https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=85','https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1200&q=85','https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1200&q=85']),
    ],
    [
        'slug'=>'manjericao', 'name'=>'Chalé Manjericão', 'category'=>'Luxo VIP · Vista jardim', 'view'=>'Vista para o jardim',
        'description'=>'Refúgio sofisticado para descanso, reconexão e permanências mais prolongadas, com estrutura completa para momentos de encontro consigo.',
        'cover'=>'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=85',
        'gallery'=>implode("\n", ['https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=85','https://images.unsplash.com/photo-1567016432779-094069958ea5?auto=format&fit=crop&w=1200&q=85','https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=1200&q=85','https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=85']),
    ],
];

foreach (['Alecrim','Capim Cidreira','Calêndula','Erva Doce','Melissa','Jasmim'] as $name) {
    $fallbackChalets[] = [
        'slug'=>chalet_slug_id($name), 'name'=>'Chalé ' . $name, 'category'=>'Standard · Vista jardim', 'view'=>'Vista jardim',
        'description'=>'Chalé aromático para casal, com aconchego, café da manhã incluído e atmosfera tranquila para adultos.',
        'cover'=>'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80',
        'gallery'=>implode("\n", ['https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80','https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=80','https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=900&q=80']),
    ];
}

$chalets = catalog_chalets($fallbackChalets);
$featured = array_values(array_filter($chalets, fn($row) => stripos((string)($row['category'] ?? ''), 'standard') === false));
$standard = array_values(array_filter($chalets, fn($row) => stripos((string)($row['category'] ?? ''), 'standard') !== false));
?>
<section class="page-hero">
  <img class="page-hero-img kenburns" src="<?= e(repair_image_url(block('chales','hero_image','https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=2000&q=80'))) ?>" alt="Chalé">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('chales','hero_eyebrow','As nossas acomodações')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] text-reveal"><?= block('chales','hero_title','Chalés <em class="serif-italic text-gold-500">com alma.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg reveal"><?= block('chales','hero_subtitle','Cada detalhe pensado com carinho, aconchego, conforto e gentilezas em contato constante com a natureza.') ?></p>
  </div>
</section>

<?php foreach ($featured as $index => $row): ?>
  <?php $dark = $index % 2 === 0; $slides = chalet_view_slides($row); ?>
  <section id="<?= e((string)$row['slug']) ?>" class="section <?= $dark ? 'bg-forest-800 text-cream-100' : 'bg-cream-100' ?> relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
      <div class="reveal <?= !$dark ? 'order-2 lg:order-1' : '' ?> rounded-md overflow-hidden shadow-2xl">
        <?php embla_carousel($slides, ['ratio'=>'4/5','autoplay'=>true,'lightbox'=>true,'group'=>(string)$row['slug']]); ?>
      </div>
      <div class="reveal <?= !$dark ? 'order-1 lg:order-2' : '' ?>">
        <span class="<?= $dark ? 'text-gold-500' : 'text-terracota-500' ?> tracking-eyebrow uppercase text-[11px]">• <?= e((string)($row['category'] ?: 'Chalé')) ?> •</span>
        <h2 class="font-editorial text-5xl md:text-6xl <?= $dark ? 'text-cream-50' : 'text-forest-900' ?> mt-3 leading-tight"><?= e((string)$row['name']) ?></h2>
        <?php if (!empty($row['view'])): ?><span class="inline-block mt-4 px-3 py-1 <?= $dark ? 'bg-gold-500 text-ink-900' : 'bg-cream-200 text-ink-800' ?> text-[10px] tracking-eyebrow uppercase rounded-full"><?= e((string)$row['view']) ?></span><?php endif; ?>
        <div class="mt-6 space-y-4 text-[16px] leading-[1.85] <?= $dark ? 'text-cream-100/85' : 'text-ink-700/90' ?>">
          <p><?= nl2br(e((string)$row['description'])) ?></p>
        </div>
        <ul class="mt-7 grid grid-cols-2 gap-3 text-[14px]">
          <li class="flex items-center gap-2"><i data-lucide="moon" class="w-4 h-4 <?= $dark ? 'text-gold-500' : 'text-terracota-500' ?>"></i> <?= e(block('chales','amenity_min_stay','Mínimo 2 noites')) ?></li>
          <li class="flex items-center gap-2"><i data-lucide="coffee" class="w-4 h-4 <?= $dark ? 'text-gold-500' : 'text-terracota-500' ?>"></i> <?= e(block('chales','amenity_breakfast','Café da manhã')) ?></li>
          <li class="flex items-center gap-2"><i data-lucide="users" class="w-4 h-4 <?= $dark ? 'text-gold-500' : 'text-terracota-500' ?>"></i> <?= e(block('chales','amenity_capacity','Acomoda 2 pessoas')) ?></li>
          <li class="flex items-center gap-2"><i data-lucide="trees" class="w-4 h-4 <?= $dark ? 'text-gold-500' : 'text-terracota-500' ?>"></i> <?= e((string)($row['view'] ?: 'Vista jardim')) ?></li>
        </ul>
        <div class="mt-8 flex flex-wrap gap-3">
          <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="<?= $dark ? 'btn-gold' : 'btn-primary' ?> magnetic"><i data-lucide="calendar-heart" class="w-4 h-4"></i> Reservar <?= e(str_replace('Chalé ', '', (string)$row['name'])) ?></a>
          <?= chalet_video_button($row, $dark) ?>
        </div>
      </div>
    </div>
  </section>
<?php endforeach; ?>

<?php if ($standard): ?>
<section id="standard" class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6">
    <div class="reveal max-w-3xl">
      <span class="text-gold-600 tracking-eyebrow uppercase text-[11px]">• <?= e(block('chales','standard_eyebrow','Standard')) ?> •</span>
      <h2 class="font-editorial text-5xl md:text-6xl text-forest-900 mt-3 leading-tight"><?= block('chales','standard_title','Chalés <em class="serif-italic text-gold-600">Aromáticos.</em>') ?></h2>
      <p class="mt-5 text-[17px] leading-[1.85] text-ink-700/85"><?= block('chales','intro_standard','Com decoração charmosa e <strong>vista para o nosso lindo e perfumado jardim</strong>, são refúgios perfeitos para momentos relaxantes. Cada chalé acomoda apenas 2 pessoas.') ?></p>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-5 reveal-stagger">
      <?php foreach($standard as $row): ?>
        <article id="<?= e((string)$row['slug']) ?>" class="card-elevated overflow-hidden">
          <?php embla_carousel(chalet_view_slides($row), ['ratio'=>'4/3','autoplay'=>false,'arrows'=>true,'dots'=>true,'lightbox'=>true,'group'=>'standard-' . chalet_slug_id((string)$row['slug'])]); ?>
          <div class="p-6">
            <h3 class="font-editorial text-2xl text-forest-900"><?= e((string)$row['name']) ?></h3>
            <p class="mt-2 text-[14px] text-ink-700/75 leading-relaxed"><?= nl2br(e((string)$row['description'])) ?></p>
            <?= chalet_video_button($row, false, 'mt-5') ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <p class="mt-10 text-center text-[14px] text-ink-700/60 serif-italic"><?= e(block('chales','standard_note','Cada chalé acomoda apenas 2 pessoas, exclusivamente para adultos.')) ?></p>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>