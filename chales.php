<?php
$pageTitle='Chalés'; $pageDesc='Chalés Lavanda, Manjericão e Aromáticos — acomodações exclusivas em Mar Vermelho, AL.'; $pageSlug='chales';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/partials.php';
function sanitize_id($s){ return preg_replace('/[^a-z0-9]+/','-', strtolower(strtr($s, ['ç'=>'c','ã'=>'a','á'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u']))); }
?>
<section class="page-hero">
  <img class="page-hero-img kenburns" src="<?= e(block('chales','hero_image','https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=2000&q=80')) ?>" alt="Chalé">
  <div class="page-hero-grad"></div>
  <div class="relative z-[1] max-w-6xl mx-auto px-6 pb-16 text-cream-50">
    <span class="text-cream-50/80 tracking-eyebrow uppercase text-[11px]"><?= e(block('chales','hero_eyebrow','As nossas acomodações')) ?></span>
    <h1 class="font-editorial text-5xl md:text-7xl mt-3 leading-[1.05] text-reveal"><?= block('chales','hero_title','Chalés <em class="serif-italic text-gold-500">com alma.</em>') ?></h1>
    <p class="mt-4 max-w-xl text-cream-100/85 text-lg reveal"><?= block('chales','hero_subtitle','Cada detalhe pensado com carinho — aconchego, conforto e gentilezas em contato constante com a natureza.') ?></p>
  </div>
</section>

<!-- LAVANDA -->
<section id="lavanda" class="section bg-forest-800 text-cream-100 relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
    <div class="reveal">
      <span class="text-gold-500 tracking-eyebrow uppercase text-[11px]">• Luxo Varanda •</span>
      <h2 class="font-editorial text-5xl md:text-6xl text-cream-50 mt-3 leading-tight">Chalé <em class="serif-italic text-gold-500">Lavanda.</em></h2>
      <span class="inline-block mt-4 px-3 py-1 bg-gold-500 text-ink-900 text-[10px] tracking-eyebrow uppercase rounded-full">Único com vista panorâmica</span>
      <div class="mt-6 space-y-4 text-[16px] leading-[1.85] text-cream-100/85">
        <p>O Chalé Luxo Varanda da Aromas da Serra é a escolha perfeita para uma hospedagem inesquecível. Com <strong>vista panorâmica</strong> para a serra, oferece decoração charmosa e todos os confortos modernos.</p>
        <p>A varanda privativa é um refúgio para momentos de relaxamento, onde se aprecia a paisagem exuberante em ambiente tranquilo e exclusivo.</p>
      </div>
      <ul class="mt-7 grid grid-cols-2 gap-3 text-[14px]">
        <li class="flex items-center gap-2"><i data-lucide="moon" class="w-4 h-4 text-gold-500"></i> Mínimo 2 noites</li>
        <li class="flex items-center gap-2"><i data-lucide="coffee" class="w-4 h-4 text-gold-500"></i> Café da manhã</li>
        <li class="flex items-center gap-2"><i data-lucide="users" class="w-4 h-4 text-gold-500"></i> Acomoda 2 pessoas</li>
        <li class="flex items-center gap-2"><i data-lucide="mountain" class="w-4 h-4 text-gold-500"></i> Vista panorâmica</li>
      </ul>
      <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-gold magnetic mt-8"><i data-lucide="calendar-heart" class="w-4 h-4"></i> Reservar Lavanda</a>
    </div>
    <div class="reveal rounded-md overflow-hidden shadow-2xl">
      <?php embla_carousel([
        ['src'=>'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=85', 'alt'=>'Quarto Lavanda', 'caption'=>'Vista panorâmica'],
        ['src'=>'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=85', 'alt'=>'Banheira'],
        ['src'=>'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=1200&q=85', 'alt'=>'Varanda privativa', 'caption'=>'Varanda'],
        ['src'=>'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1200&q=85', 'alt'=>'Detalhes'],
      ], ['ratio'=>'4/5','autoplay'=>true,'lightbox'=>true,'group'=>'lavanda']); ?>
    </div>
  </div>
</section>

<!-- MANJERICÃO -->
<section id="manjericao" class="section bg-cream-100">
  <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
    <div class="reveal rounded-md overflow-hidden shadow-2xl order-2 lg:order-1">
      <?php embla_carousel([
        ['src'=>'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=85', 'alt'=>'Sala Manjericão'],
        ['src'=>'https://images.unsplash.com/photo-1567016432779-094069958ea5?auto=format&fit=crop&w=1200&q=85', 'alt'=>'Quarto'],
        ['src'=>'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=1200&q=85', 'alt'=>'Cozinha integrada', 'caption'=>'Cozinha'],
        ['src'=>'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1200&q=85', 'alt'=>'Detalhe'],
      ], ['ratio'=>'4/5','autoplay'=>true,'lightbox'=>true,'group'=>'manjericao']); ?>
    </div>
    <div class="reveal order-1 lg:order-2">
      <span class="text-terracota-500 tracking-eyebrow uppercase text-[11px]">• Luxo VIP •</span>
      <h2 class="font-editorial text-5xl md:text-6xl text-forest-900 mt-3 leading-tight">Chalé <em class="serif-italic text-terracota-500">Manjericão.</em></h2>
      <span class="inline-block mt-4 px-3 py-1 bg-cream-200 text-ink-800 text-[10px] tracking-eyebrow uppercase rounded-full">Vista para o jardim</span>
      <div class="mt-6 space-y-4 text-[16px] leading-[1.85] text-ink-700/90">
        <p>Charmoso e sofisticado, o Manjericão é um refúgio perfeito para quem busca descanso e tranquilidade — com estrutura completa para momentos de encontro consigo mesmo, reconexão e reflexão.</p>
        <p>Projetado para vivências sabáticas, dispõe de estrutura ideal para permanências mais prolongadas. Um convite ao bem-estar e à felicidade.</p>
      </div>
      <ul class="mt-7 grid grid-cols-2 gap-3 text-[14px]">
        <li class="flex items-center gap-2"><i data-lucide="moon" class="w-4 h-4 text-terracota-500"></i> Mínimo 2 noites</li>
        <li class="flex items-center gap-2"><i data-lucide="coffee" class="w-4 h-4 text-terracota-500"></i> Café da manhã</li>
        <li class="flex items-center gap-2"><i data-lucide="users" class="w-4 h-4 text-terracota-500"></i> Acomoda 2 pessoas</li>
        <li class="flex items-center gap-2"><i data-lucide="trees" class="w-4 h-4 text-terracota-500"></i> Vista jardim</li>
      </ul>
      <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-primary magnetic mt-8"><i data-lucide="calendar-heart" class="w-4 h-4"></i> Reservar Manjericão</a>
    </div>
  </div>
</section>

<!-- STANDARD -->
<section id="standard" class="section bg-cream-50">
  <div class="max-w-7xl mx-auto px-6">
    <div class="reveal max-w-3xl">
      <span class="text-gold-600 tracking-eyebrow uppercase text-[11px]">• Standard •</span>
      <h2 class="font-editorial text-5xl md:text-6xl text-forest-900 mt-3 leading-tight">Chalés <em class="serif-italic text-gold-600">Aromáticos.</em></h2>
      <p class="mt-5 text-[17px] leading-[1.85] text-ink-700/85"><?= block('chales','intro_standard','Com decoração charmosa e <strong>vista para o nosso lindo e perfumado jardim</strong>, são refúgios perfeitos para momentos relaxantes. Cada chalé acomoda apenas 2 pessoas.') ?></p>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-5 reveal-stagger">
      <?php
      $aromas = [
        ['Alecrim',       ['photo-1565299624946-b28f40a0ae38','photo-1611892440504-42a792e24d32','photo-1591088398332-8a7791972843']],
        ['Capim Cidreira',['photo-1560448204-e02f11c3d0e2','photo-1566073771259-6a8506099945','photo-1505693416388-ac5ce068fe85']],
        ['Calêndula',     ['photo-1490750967868-88aa4486c946','photo-1567016432779-094069958ea5','photo-1556909114-f6e7ad7d3136']],
        ['Erva Doce',     ['photo-1471666875520-c75081f42081','photo-1505691938895-1758d7feb511','photo-1611892440504-42a792e24d32']],
        ['Melissa',       ['photo-1527842891421-42eec6e703ea','photo-1582719478250-c89cae4dc85b','photo-1540541338287-41700207dee6']],
        ['Jasmim',        ['photo-1591088398332-8a7791972843','photo-1505693416388-ac5ce068fe85','photo-1598300042247-d088f8ab3a91']],
      ];
      foreach($aromas as [$n,$ph]):
        $slides = array_map(fn($p) => ['src' => "https://images.unsplash.com/$p?auto=format&fit=crop&w=900&q=80", 'alt' => "Chalé $n"], $ph);
      ?>
        <article class="card-elevated overflow-hidden">
          <?php embla_carousel($slides, ['ratio'=>'4/3','autoplay'=>false,'arrows'=>true,'dots'=>true,'lightbox'=>true,'group'=>'standard-' . sanitize_id($n)]); ?>
          <div class="p-6">
            <h3 class="font-editorial text-2xl text-forest-900">Chalé <?= e($n) ?></h3>
            <p class="mt-2 text-[14px] text-ink-700/75">Casal ou solteiro · Mínimo 2 noites · Café da manhã incluído.</p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <p class="mt-10 text-center text-[14px] text-ink-700/60 serif-italic">Cada chalé acomoda apenas 2 pessoas — exclusivamente para adultos.</p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
