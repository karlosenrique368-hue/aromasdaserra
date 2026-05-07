<?php
require __DIR__ . '/bootstrap.php';
require_admin();
$current='messages.php'; $pageTitle='Mensagens'; $pageEyebrow='Inbox';

$pdo = db();

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check()) {
    $op = $_POST['op'] ?? '';
    if ($op==='read') $pdo->prepare('UPDATE messages SET is_read=1 WHERE id=?')->execute([(int)$_POST['id']]);
    if ($op==='delete') $pdo->prepare('DELETE FROM messages WHERE id=?')->execute([(int)$_POST['id']]);
    flash('Mensagem atualizada.');
    header('Location: ' . admin_url('messages.php')); exit;
}

$rows = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
require __DIR__ . '/partials/layout_top.php';
?>
<div class="page-head">
  <div><span class="eyebrow">Contato</span><h2>Mensagens <em>recebidas</em>.</h2></div>
</div>

<?php if (!$rows): ?>
  <div class="adm-card"><div class="empty"><i data-lucide="mail-open"></i><h4>Nenhuma mensagem</h4></div></div>
<?php else: ?>
  <div style="display:flex; flex-direction:column; gap:.7rem;">
  <?php foreach ($rows as $m): ?>
    <div class="adm-card" style="<?= $m['is_read']?'opacity:.78':'border-left:3px solid var(--a-terracota);' ?>">
      <div style="display:flex; justify-content:space-between; align-items:start; gap:1rem; flex-wrap:wrap;">
        <div>
          <strong style="font-size:15px;"><?= ee($m['name']) ?></strong>
          <?php if (!$m['is_read']): ?><span class="badge new" style="margin-left:.5rem;">novo</span><?php endif; ?>
          <div style="font-size:12px; color:var(--a-muted); margin-top:.15rem;">
            <?php if ($m['email']): ?><a href="mailto:<?= ee($m['email']) ?>"><?= ee($m['email']) ?></a> · <?php endif; ?>
            <?php if ($m['phone']): ?><?= ee($m['phone']) ?> · <?php endif; ?>
            <?= ee(date('d/m/Y H:i', strtotime($m['created_at']))) ?>
          </div>
          <?php if ($m['subject']): ?><div style="font-family:'Italiana',serif; font-size:1.1rem; color:var(--a-forest-deep); margin-top:.4rem;"><?= ee($m['subject']) ?></div><?php endif; ?>
          <p style="margin:.4rem 0 0; font-size:14px; line-height:1.6; color:var(--a-text);"><?= nl2br(ee($m['body'] ?? '')) ?></p>
        </div>
        <div style="display:flex; gap:.4rem;">
          <?php if (!$m['is_read']): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="op" value="read"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button class="btn btn-ghost" type="submit"><i data-lucide="check"></i> Marcar lida</button></form>
          <?php endif; ?>
          <form method="post" onsubmit="return confirm('Remover?');"><?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= $m['id'] ?>"><button class="btn btn-danger" type="submit"><i data-lucide="trash-2"></i></button></form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/layout_bot.php'; ?>
