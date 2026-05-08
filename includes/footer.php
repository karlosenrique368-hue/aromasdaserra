</main>

<!-- Marquee bar -->
<div class="marquee-bar" aria-hidden="true">
  <?php $marquee = [block('global','marquee_1','Mar Vermelho · AL'), block('global','marquee_2','Cozinha Mediterrânea'), block('global','marquee_3','Ritual da Fogueira'), block('global','marquee_4','Ritual do Chá da Tarde'), block('global','marquee_5','Temporada de Fondue'), block('global','marquee_6','Suíça Alagoana')]; ?>
  <span>
    <?php foreach ($marquee as $item): ?><span><?= e($item) ?></span><i data-lucide="leaf" class="w-4 h-4"></i><?php endforeach; ?>
  </span>
  <span aria-hidden="true">
    <?php foreach ($marquee as $item): ?><span><?= e($item) ?></span><i data-lucide="leaf" class="w-4 h-4"></i><?php endforeach; ?>
  </span>
</div>

<!-- CTA Strip -->
<section class="relative overflow-hidden bg-forest-900 text-cream-100 noise">
  <div class="absolute inset-0 opacity-[0.08] bg-[radial-gradient(circle_at_30%_50%,#c4a46c_0,transparent_60%)]"></div>
  <div class="relative max-w-6xl mx-auto px-6 py-20 md:py-28 text-center">
    <span class="eyebrow text-gold-500/80"><?= e(block('global','cta_eyebrow','— Reserve sua estadia —')) ?></span>
    <h2 class="font-editorial text-4xl md:text-6xl text-cream-50 mt-4 leading-[1.05]"><?= block('global','cta_title','Frio da serra, gastronomia, requinte e <em class="serif-italic text-gold-500">hospitalidade.</em>') ?></h2>
    <p class="mt-5 max-w-2xl mx-auto text-cream-100/80 text-lg leading-relaxed">
      <?= block('global','cta_body','Faça sua reserva e venha vivenciar momentos especiais em meio ao silêncio da natureza, acompanhado de uma cozinha mediterrânea cheia de aromas e sabores.') ?>
    </p>
    <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
      <a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" class="btn-gold magnetic"><i data-lucide="message-circle" class="w-4 h-4"></i> Reservar pelo WhatsApp</a>
      <a href="tel:+<?= SITE_PHONE_RAW ?>" class="btn-ghost-light"><?= e(SITE_PHONE_DISPLAY) ?></a>
    </div>
  </div>
</section>

<footer class="bg-ink-900 text-cream-100/80">
  <div class="max-w-8xl mx-auto px-6 py-16 grid md:grid-cols-4 gap-10">
    <div class="md:col-span-2">
      <div class="flex items-center gap-3">
        <span class="brand-mark brand-mark--invert" style="width:84px;height:84px;"><img src="<?= asset('img/logoserra.jpg') ?>" alt="<?= e(SITE_NAME) ?>"></span>
      </div>
      <p class="mt-5 text-[15px] leading-relaxed max-w-md">
        <?= block('global','footer_about','Refúgio boutique em ' . e(SITE_LOCATION) . ', na Suíça Alagoana. Hospedagem exclusiva para adultos, gastronomia mediterrânea e contemplação em meio à serra.') ?>
      </p>
      <div class="mt-6 flex items-center gap-3">
        <a href="<?= e(SITE_INSTAGRAM) ?>" target="_blank" rel="noopener" class="social-pill" aria-label="Instagram"><i data-lucide="instagram" class="w-4 h-4"></i></a>
        <a href="<?= e(SITE_FACEBOOK) ?>"  target="_blank" rel="noopener" class="social-pill" aria-label="Facebook"><i data-lucide="facebook"  class="w-4 h-4"></i></a>
        <a href="<?= e(SITE_WHATSAPP) ?>"  target="_blank" rel="noopener" class="social-pill" aria-label="WhatsApp"><i data-lucide="message-circle" class="w-4 h-4"></i></a>
      </div>
    </div>
    <div>
      <h4 class="footer-title">Navegação</h4>
      <ul class="space-y-2 text-[14px]">
        <li><a class="footer-link" href="<?= url('a-pousada.php') ?>">A Pousada</a></li>
        <li><a class="footer-link" href="<?= url('depoimentos.php') ?>">Depoimentos</a></li>
        <li><a class="footer-link" href="<?= url('chales.php') ?>">Chalés</a></li>
        <li><a class="footer-link" href="<?= url('gastronomia.php') ?>">Gastronomia</a></li>
        <li><a class="footer-link" href="<?= url('taberna.php') ?>">Taberna do Monge</a></li>
        <li><a class="footer-link" href="<?= url('produtos.php') ?>">Produtos Artesanais</a></li>
        <li><a class="footer-link" href="<?= url('experiencias.php') ?>">Experiências</a></li>
        <li><a class="footer-link" href="<?= url('localizacao.php') ?>">Localização</a></li>
        <li><a class="footer-link" href="<?= url('itinerario.php') ?>">Itinerário</a></li>
      </ul>
    </div>
    <div>
      <h4 class="footer-title">Contato</h4>
      <ul class="space-y-2 text-[14px]">
        <li class="flex items-start gap-2"><i data-lucide="map-pin" class="w-4 h-4 mt-0.5 text-gold-500"></i> <?= e(SITE_LOCATION) ?></li>
        <li class="flex items-start gap-2"><i data-lucide="phone" class="w-4 h-4 mt-0.5 text-gold-500"></i> <a href="tel:+<?= SITE_PHONE_RAW ?>" class="footer-link"><?= e(SITE_PHONE_DISPLAY) ?></a></li>
        <li class="flex items-start gap-2"><i data-lucide="mail" class="w-4 h-4 mt-0.5 text-gold-500"></i> <a href="mailto:<?= e(SITE_EMAIL) ?>" class="footer-link break-all"><?= e(SITE_EMAIL) ?></a></li>
      </ul>
    </div>
  </div>
  <div class="border-t border-cream-100/10">
    <div class="max-w-8xl mx-auto px-6 py-5 text-[12px] flex flex-wrap items-center justify-between gap-3">
      <span>© <?= date('Y') ?> Pousada Aromas da Serra · Todos os direitos reservados.</span>
      <span class="text-cream-100/50"><?= e(block('global','footer_signature','Suíça Alagoana · Mar Vermelho — AL')) ?></span>
    </div>
  </div>
</footer>

<a href="<?= e(SITE_WHATSAPP) ?>" target="_blank" rel="noopener" id="wa-float" aria-label="Falar no WhatsApp">
  <i data-lucide="message-circle"></i>
  <span>Reserve agora</span>
</a>

<button id="to-top" aria-label="Voltar ao topo"><i data-lucide="arrow-up"></i></button>

<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
