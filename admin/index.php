<?php
require __DIR__ . '/bootstrap.php';
require_admin();
$current='index.php'; $pageTitle='Visão geral'; $pageEyebrow='Painel';

$pdo = db();
$stats = [
    'leads_new'  => (int)$pdo->query("SELECT COUNT(*) FROM leads WHERE status='new'")->fetchColumn(),
    'leads_total'=> (int)$pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn(),
    'msg_unread' => (int)$pdo->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn(),
    'chalets'    => (int)$pdo->query("SELECT COUNT(*) FROM chalets WHERE is_active=1")->fetchColumn(),
    'exps'       => (int)$pdo->query("SELECT COUNT(*) FROM experiences WHERE is_active=1")->fetchColumn(),
    'products'   => (int)$pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn(),
    'testimonials'=> (int)$pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn(),
    'gallery'    => (int)$pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn(),
];
$recentLeads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 6")->fetchAll();
$recentMsgs  = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

require __DIR__ . '/partials/layout_top.php';
?>
<div class="page-head">
  <div>
    <span class="eyebrow">Bem-vinda</span>
    <h2>Visão <em>geral</em>.</h2>
  </div>
  <a href="<?= ee(admin_url('leads.php')) ?>" class="btn btn-primary"><i data-lucide="calendar-heart"></i> Ver reservas</a>
</div>

<div class="stat-grid">
  <div class="stat"><span class="ico"><i data-lucide="calendar-heart"></i></span><div class="label">Reservas novas</div><div class="value"><?= $stats['leads_new'] ?></div></div>
  <div class="stat"><span class="ico"><i data-lucide="users"></i></span><div class="label">Total leads</div><div class="value"><?= $stats['leads_total'] ?></div></div>
  <div class="stat"><span class="ico"><i data-lucide="mail"></i></span><div class="label">Mensagens não lidas</div><div class="value"><?= $stats['msg_unread'] ?></div></div>
  <div class="stat"><span class="ico"><i data-lucide="home"></i></span><div class="label">Chalés ativos</div><div class="value"><?= $stats['chalets'] ?></div></div>
  <div class="stat"><span class="ico"><i data-lucide="sparkles"></i></span><div class="label">Experiências</div><div class="value"><?= $stats['exps'] ?></div></div>
  <div class="stat"><span class="ico"><i data-lucide="shopping-bag"></i></span><div class="label">Produtos</div><div class="value"><?= $stats['products'] ?></div></div>
  <div class="stat"><span class="ico"><i data-lucide="quote"></i></span><div class="label">Depoimentos</div><div class="value"><?= $stats['testimonials'] ?></div></div>
  <div class="stat"><span class="ico"><i data-lucide="image"></i></span><div class="label">Imagens galeria</div><div class="value"><?= $stats['gallery'] ?></div></div>
</div>

<div style="display:grid; grid-template-columns:1.6fr 1fr; gap:1.25rem; margin-top:1.5rem;" class="dash-grid">
  <div class="adm-card">
    <h3>Últimas reservas</h3>
    <?php if (!$recentLeads): ?>
      <div class="empty"><i data-lucide="inbox"></i><h4>Sem reservas ainda</h4><p>As solicitações enviadas pelo site aparecerão aqui.</p></div>
    <?php else: ?>
      <table class="adm-table" style="border:0; box-shadow:none;">
        <thead><tr><th>Hóspede</th><th>Check-in</th><th>Chalé</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($recentLeads as $l): ?>
          <tr>
            <td><strong><?= ee($l['name']) ?></strong><div style="font-size:12px; color:var(--a-muted)"><?= ee($l['email'] ?: $l['phone']) ?></div></td>
            <td><?= ee($l['checkin'] ?: '—') ?></td>
            <td><?= ee($l['chalet_slug'] ?: '—') ?></td>
            <td><span class="badge <?= $l['status']==='new'?'new':($l['status']==='confirmed'?'success':'muted') ?>"><?= ee($l['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <div class="adm-card">
    <h3>Mensagens recentes</h3>
    <?php if (!$recentMsgs): ?>
      <div class="empty"><i data-lucide="mail-open"></i><h4>Caixa vazia</h4></div>
    <?php else: ?>
      <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:.6rem;">
      <?php foreach ($recentMsgs as $m): ?>
        <li style="padding:.7rem .8rem; border:1px solid var(--a-border-soft); border-radius:8px;">
          <div style="display:flex; justify-content:space-between; gap:.5rem; align-items:start;">
            <strong style="font-size:14px;"><?= ee($m['name']) ?></strong>
            <?php if (!$m['is_read']): ?><span class="badge new">novo</span><?php endif; ?>
          </div>
          <div style="font-size:13px; color:var(--a-muted); margin-top:.2rem;"><?= ee(mb_strimwidth($m['body'] ?? '', 0, 80, '…')) ?></div>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>
<style>@media(max-width:980px){.dash-grid{grid-template-columns:1fr !important;}}</style>

<?php require __DIR__ . '/partials/layout_bot.php'; ?>
