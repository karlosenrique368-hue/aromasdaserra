<?php
$pageTitle = 'Experiências';
$pageDesc  = 'Vivências exclusivas da Pousada Aromas da Serra: chá de boas-vindas, fogueira, gastronomia, fondue, mandala e espaços de contemplação.';
$pageSlug  = 'experiencias';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/partials.php';

function exp_group_id(string $title): string {
    return preg_replace('/[^a-z0-9]+/', '-', strtolower(strtr($title, ['ç'=>'c','ã'=>'a','á'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','—'=>'-'])));
}

function exp_view_slides(array $row): array {
    $slides = gallery_slides((string)($row['gallery'] ?? ''), (string)($row['title'] ?? 'Experiência'));
    if (!$slides && !empty($row['cover'])) $slides[] = ['src' => repair_image_url((string)$row['cover']), 'alt' => (string)$row['title']];
    return $slides;
}

$fallbackGallery = implode("\n", [
  'https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=1200&q=85',
  'https://images.unsplash.com/photo-1455218873509-8097305ee378?auto=format&fit=crop&w=1200&q=85',
]);
$fallbackExperiences = [
  ['slug'=>'cha-de-boas-vindas','title'=>'Chá de Boas-Vindas','icon'=>'coffee','description'=>'Inspirado em uma prática milenar de origem japonesa, o Chá de Boas-Vindas marca a chegada com respeito, presença e paz de espírito. Na Aromas da Serra, ele ganha personalidade aromática, com ervas selecionadas por suas propriedades de cuidado e relaxamento, convidando corpo e mente a desacelerar.','cover'=>'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'ritual-da-fogueira','title'=>'Ritual da Fogueira · Magia do Fogo','icon'=>'flame','description'=>'O fogo é nosso aliado sagrado: ilumina, aquece, acolhe e transmuta. Ao redor da fogueira celebramos fé, renovação e encontro, reconhecendo os muitos significados que esse elemento carrega para diferentes povos. Bem-vindo ao nosso encontro místico da fogueira.','cover'=>'https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'experiencia-gastronomica','title'=>'Experiência Gastronômica','icon'=>'utensils','description'=>'As receitas da pousada nascem de uma vivência cultural pela Suíça, pelo sul da França, pela Itália e pela região mediterrânea. Ingredientes frescos, ervas colhidas na horta e técnicas adaptadas ao estilo brasileiro resultam em pratos autorais, delicados e cheios de aromas.','cover'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'fondue-de-queijo-moitie-moitie','title'=>'Fondue de Queijo Moitié-Moitié','icon'=>'cooking-pot','description'=>'Inspirado nos Alpes Suíços, o Fondue de Queijo Moitié-Moitié combina a delicadeza de queijos mais suaves com a intensidade de queijos marcantes. Preparado artesanalmente e servido à mesa, é o grande protagonista da temporada de inverno: uma forma de se sentir nos Alpes sem sair de Mar Vermelho.','cover'=>'https://images.unsplash.com/photo-1485921325833-c519f76c4927?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'taberna-do-monge','title'=>'Taberna do Monge','icon'=>'wine','description'=>'Inspirada nas antigas tabernas medievais, a Taberna do Monge combina decoração rústica, atmosfera acolhedora e uma lareira central que aquece o ambiente. É um espaço intimista para comer bem, conversar sem pressa e celebrar os sabores da serra. Bon appétit.','cover'=>'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'espaco-contemplacao','title'=>'Espaço Contemplação','icon'=>'mountain','description'=>'Um ambiente pensado para meditação, silêncio e respiração profunda, com vista privilegiada para as montanhas e para a vegetação ao redor. É o lugar ideal para desacelerar do ritmo da vida moderna e reencontrar presença.','cover'=>'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'espaco-gourmet','title'=>'Espaço Gourmet','icon'=>'chef-hat','description'=>'Palco de bons encontros e experiências gastronômicas compartilhadas, o Espaço Gourmet convida hóspedes a criar, inovar e apresentar suas vivências culinárias com novos temperos, aromas e harmonizações especiais. Venha desfrutar desse momento e traga seus amigos.','cover'=>'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'caminho-das-pedras','title'=>'Caminho das Pedras','icon'=>'footprints','description'=>'Inspirado nos cinco pilares de Sebastian Kneipp — água, movimento, alimentação, plantas medicinais e estilo de vida — o Caminho das Pedras propõe equilíbrio e presença. A experiência convida o corpo a despertar seus recursos naturais de cuidado.','cover'=>'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'nossa-piscina','title'=>'Nossa Piscina','icon'=>'waves','description'=>'Um pequeno oásis de tranquilidade e beleza natural. Com águas cristalinas e vista deslumbrante, a piscina é perfeita para relaxar, refrescar-se e contemplar a natureza exuberante ao redor.','cover'=>'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'mandala','title'=>'Mandala','icon'=>'flower-2','description'=>'A Mandala representa o universo, a essência e a jornada espiritual de cada pessoa. Seus círculos simbolizam continuidade, conexão e harmonia. Na pousada, ela também expressa a arte de plantar, cultivar e colher aromas para a cozinha, além de inspirar tranquilidade, serenidade e concentração plena.','cover'=>'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'espaco-leitura','title'=>'Espaço Leitura','icon'=>'book-open','description'=>'Ler estimula o raciocínio, expande a imaginação e cria outros mundos dentro de nós. O Espaço Leitura é um convite para pausar, silenciar e se conectar com novas ideias em meio ao clima sereno da pousada.','cover'=>'https://images.unsplash.com/photo-1519682337058-a94d519337bc?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
];
$experiences = catalog_experiences($fallbackExperiences);
?>
<section class="page-hero">
  <img class="page-hero-img kenburns" src="<?= e(repair_image_url(block('experiencias','hero_image','https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=2000&q=80'))) ?>" alt="Experiências">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('experiencias','hero_eyebrow','Vivências na Serra')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] text-reveal"><?= block('experiencias','hero_title','Rituais que <em class="serif-italic text-gold-500">tocam a alma.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg reveal"><?= block('experiencias','hero_subtitle','Experiências autorais — desenhadas para o reencontro com o tempo, com a natureza e com você mesmo.') ?></p>
  </div>
</section>

<section class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6">
    <header class="reveal max-w-3xl">
      <span class="eyebrow"><?= e(block('experiencias','rituals_eyebrow','Vivências da pousada')) ?></span>
      <h2 class="font-editorial text-4xl md:text-5xl text-forest-900 mt-3 leading-tight"><?= block('experiencias','rituals_title','Cada experiência, um <em class="serif-italic text-terracota-500">presente.</em>') ?></h2>
      <p class="gallery-mobile-hint !justify-start"><i data-lucide="maximize-2" class="w-4 h-4"></i> <?= e(block('experiencias','rituals_hint','Nas fotos, toque para ampliar e navegar pelo lightbox.')) ?></p>
    </header>

    <div class="mt-12 grid lg:grid-cols-2 gap-8 reveal-stagger">
      <?php foreach($experiences as $row): ?>
        <article id="<?= e((string)$row['slug']) ?>" class="card-elevated overflow-hidden">
          <div class="relative">
            <?php embla_carousel(exp_view_slides($row), ['ratio'=>'16/10','autoplay'=>true,'lightbox'=>true,'group'=>exp_group_id((string)$row['title'])]); ?>
            <span class="absolute top-4 right-4 z-[2] bg-cream-50 text-terracota-500 w-11 h-11 grid place-items-center rounded-full shadow-md"><i data-lucide="<?= e((string)($row['icon'] ?: 'sparkles')) ?>" class="w-5 h-5"></i></span>
          </div>
          <div class="p-7">
            <h3 class="font-editorial text-2xl text-forest-900"><?= e((string)$row['title']) ?></h3>
            <p class="mt-2 text-[15px] text-ink-700/80 leading-relaxed"><?= nl2br(e((string)$row['description'])) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section bg-forest-900 text-cream-100 noise relative overflow-hidden">
  <div class="absolute inset-0 opacity-[0.06] bg-[radial-gradient(circle_at_30%_70%,#c4a46c_0,transparent_55%)]"></div>
  <div class="relative max-w-6xl mx-auto px-6 text-center">
    <span class="eyebrow text-gold-500/80"><?= e(block('experiencias','benefits_eyebrow','Bem-estar incluso')) ?></span>
    <h2 class="font-editorial text-4xl md:text-5xl text-cream-50 mt-3"><?= block('experiencias','benefits_title','Cuidado em cada <em class="serif-italic text-gold-500">detalhe.</em>') ?></h2>
    <div class="mt-12 grid md:grid-cols-3 gap-10 reveal-stagger">
      <div><i data-lucide="leaf" class="w-9 h-9 mx-auto text-gold-500"></i><h3 class="font-editorial text-2xl text-cream-50 mt-4"><?= e(block('experiencias','benefit_1_title','Espaço Redário')) ?></h3><p class="text-cream-100/75 mt-2"><?= e(block('experiencias','benefit_1_body','Para a leitura e a contemplação no jardim aromático.')) ?></p></div>
      <div><i data-lucide="book-open" class="w-9 h-9 mx-auto text-gold-500"></i><h3 class="font-editorial text-2xl text-cream-50 mt-4"><?= e(block('experiencias','benefit_2_title','Biblioteca')) ?></h3><p class="text-cream-100/75 mt-2"><?= e(block('experiencias','benefit_2_body','Curadoria de livros para acompanhar o silêncio da serra.')) ?></p></div>
      <div><i data-lucide="wine" class="w-9 h-9 mx-auto text-gold-500"></i><h3 class="font-editorial text-2xl text-cream-50 mt-4"><?= e(block('experiencias','benefit_3_title','Carta de Vinhos')) ?></h3><p class="text-cream-100/75 mt-2"><?= e(block('experiencias','benefit_3_body','Rótulos selecionados para harmonizar cada momento.')) ?></p></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>