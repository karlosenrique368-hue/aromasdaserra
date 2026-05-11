<?php
$pageTitle = 'Experiências';
$pageDesc  = 'Vivências exclusivas da Pousada Aromas da Serra: drink de boas-vindas, chá da tarde, fogueira, gastronomia, fondue, mandala e espaços de contemplação.';
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
  ['slug'=>'drink-de-boas-vindas','title'=>'Drink de Boas-Vindas','icon'=>'glass-water','description'=>'O drink de boas-vindas marca a chegada com frescor, delicadeza e presença. Preparado para receber cada hóspede com cuidado, ele abre a estadia de forma leve e acolhedora, como um primeiro brinde ao descanso na serra.','cover'=>'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'cha-da-tarde','title'=>'Chá da Tarde','icon'=>'coffee','description'=>'O chá da tarde é uma experiência própria da hospedagem, pensada para desacelerar o dia com bolos artesanais, pães, infusões e conversas tranquilas. É uma pausa afetuosa, sem pressa, para saborear a serra em outro ritmo.','cover'=>'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'ritual-da-fogueira','title'=>'Ritual da Fogueira · Magia do Fogo','icon'=>'flame','description'=>'O fogo é nosso aliado sagrado: ilumina, aquece, acolhe e transmuta. Ao redor da fogueira celebramos fé, renovação e encontro, reconhecendo os muitos significados que esse elemento carrega para diferentes povos. Bem-vindo ao nosso encontro místico da fogueira.','cover'=>'https://images.unsplash.com/photo-1542367592-8849eb950fd8?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'experiencia-gastronomica','title'=>'Experiência Gastronômica','icon'=>'utensils','description'=>'As receitas da pousada nascem de uma vivência cultural pela Suíça, pelo sul da França, pela Itália e pela região mediterrânea. Ingredientes frescos, ervas colhidas na horta e técnicas adaptadas ao estilo brasileiro resultam em pratos autorais, delicados e cheios de aromas.','cover'=>'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'fondue-de-queijo','title'=>'Fondue de Queijo','icon'=>'cooking-pot','description'=>'Inspirado no clima dos Alpes Suíços, o fondue de queijo é preparado artesanalmente e servido à mesa como protagonista da temporada de inverno. Cremoso, generoso e perfeito para compartilhar, ele traduz o aconchego de Mar Vermelho em uma experiência gastronômica especial.','cover'=>'https://images.unsplash.com/photo-1485921325833-c519f76c4927?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'taberna-do-monge','title'=>'Taberna do Monge','icon'=>'wine','description'=>'A Taberna do Monge nasceu como um projeto afetivo dos diretores, inspirado no imaginário das antigas tabernas e na vontade de criar um lugar de encontro verdadeiro. A decoração rústica, a lareira central, os vinhos selecionados e a cozinha autoral constroem uma atmosfera íntima, feita para comer bem, conversar sem pressa e celebrar os sabores da serra.','cover'=>'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'espaco-contemplacao','title'=>'Espaço Contemplação','icon'=>'mountain','description'=>'Um ambiente pensado para meditação, silêncio e respiração profunda, com vista privilegiada para as montanhas e para a vegetação ao redor. É o lugar ideal para desacelerar do ritmo da vida moderna e reencontrar presença.','cover'=>'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'espaco-gourmet','title'=>'Espaço Gourmet','icon'=>'chef-hat','description'=>'Palco de bons encontros e experiências gastronômicas compartilhadas, o Espaço Gourmet convida hóspedes a criar, inovar e apresentar suas vivências culinárias com novos temperos, aromas e harmonizações especiais. Venha desfrutar desse momento e traga seus amigos.','cover'=>'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
  ['slug'=>'caminho-das-pedras','title'=>'Caminho das Pedras','icon'=>'footprints','description'=>'O Caminho das Pedras é um percurso de cuidado criado para conduzir o hóspede a uma presença mais profunda. Inspirado nos cinco pilares de Sebastian Kneipp, água, movimento, alimentação, plantas medicinais e estilo de vida, ele une contato com a natureza, silêncio e intenção. Mais do que um trajeto pelo bosque, é um convite para sentir o corpo, acalmar a mente e reconhecer pequenos rituais de equilíbrio no cotidiano.','cover'=>'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=85','gallery'=>$fallbackGallery],
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
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg reveal"><?= block('experiencias','hero_subtitle','Experiências autorais desenhadas para o reencontro com o tempo, com a natureza e com você mesmo.') ?></p>
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