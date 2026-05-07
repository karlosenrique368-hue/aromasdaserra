<?php
require __DIR__ . '/bootstrap.php';
require_admin();
$current='leads.php'; $pageTitle='Reservas'; $pageEyebrow='Gestão';

$pdo = db();

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check()) {
    $op = $_POST['op'] ?? '';
    if ($op==='status') {
        $pdo->prepare('UPDATE leads SET status=? WHERE id=?')->execute([trim($_POST['status']), (int)$_POST['id']]);
        flash('Status atualizado.');
    } elseif ($op==='delete') {
        $pdo->prepare('DELETE FROM leads WHERE id=?')->execute([(int)$_POST['id']]);
        flash('Reserva removida.');
    }
    header('Location: ' . admin_url('leads.php')); exit;
}

$filter = $_GET['filter'] ?? 'all';
$where = $filter==='all' ? '' : "WHERE status=" . db()->quote($filter);
$rows = $pdo->query("SELECT * FROM leads $where ORDER BY created_at DESC")->fetchAll();

require __DIR__ . '/partials/layout_top.php';
?>
<div class="page-head">
  <div><span class="eyebrow">Solicitações</span><h2>Reservas <em>recebidas</em>.</h2></div>
  <div style="display:flex; gap:.4rem;">
    <?php foreach (['all'=>'Todas','new'=>'Novas','contacted'=>'Contatadas','confirmed'=>'Confirmadas','closed'=>'Encerradas'] as $k=>$lbl): ?>
      <a href="?filter=<?= $k ?>" class="btn <?= $filter===$k?'btn-primary':'btn-ghost' ?>"><?= ee($lbl) ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if (!$rows): ?>
  <div class="adm-card"><div class="empty"><i data-lucide="inbox"></i><h4>Sem reservas neste filtro</h4></div></div>
<?php else: ?>
  <table class="adm-table">
    <thead><tr><th>Hóspede</th><th>Contato</th><th>Datas</th><th>Pessoas</th><th>Chalé</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $l): ?>
      <tr>
        <td><strong><?= ee($l['name']) ?></strong><div style="font-size:11px; color:var(--a-muted);"><?= ee(date('d/m/Y H:i', strtotime($l['created_at']))) ?></div></td>
        <td>
          <?php if ($l['email']): ?><a href="mailto:<?= ee($l['email']) ?>" style="font-size:13px;"><?= ee($l['email']) ?></a><br><?php endif; ?>
          <?php if ($l['phone']): ?><a href="https://wa.me/<?= preg_replace('/\D/','',$l['phone']) ?>" target="_blank" style="font-size:13px;"><?= ee($l['phone']) ?></a><?php endif; ?>
        </td>
        <td style="font-size:13px;"><?= ee($l['checkin'] ?: '—') ?> →<br><?= ee($l['checkout'] ?: '—') ?></td>
        <td><?= (int)$l['guests'] ?: '—' ?></td>
        <td><?= ee($l['chalet_slug'] ?: '—') ?></td>
        <td>
          <form method="post" style="margin:0;">
            <?= csrf_field() ?><input type="hidden" name="op" value="status"><input type="hidden" name="id" value="<?= $l['id'] ?>">
            <select name="status" onchange="this.form.submit()" style="padding:.3rem .5rem; border:1px solid var(--a-border); border-radius:6px; font-size:12px;">
              <?php foreach (['new'=>'novo','contacted'=>'contatado','confirmed'=>'confirmado','closed'=>'encerrado'] as $k=>$v): ?>
                <option value="<?= $k ?>" <?= $l['status']===$k?'selected':'' ?>><?= ee($v) ?></option>
              <?php endforeach; ?>
            </select>
          </form>
          <?php if ($l['message']): ?><div style="font-size:12px; color:var(--a-muted); margin-top:.3rem; max-width:200px;"><?= ee(mb_strimwidth($l['message'], 0, 60, '…')) ?></div><?php endif; ?>
        </td>
        <td>
          <form method="post" onsubmit="return confirm('Remover esta reserva?');" style="margin:0;">
            <?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= $l['id'] ?>">
            <button class="btn-icon" type="submit" style="color:#9a3a3a;"><i data-lucide="trash-2"></i></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require __DIR__ . '/partials/layout_bot.php'; ?>
