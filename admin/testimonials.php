<?php
require __DIR__ . '/bootstrap.php';
require_admin();
$current='testimonials.php'; $pageTitle='Depoimentos'; $pageEyebrow='Social proof';

$pdo = db();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check()) {
    $op = $_POST['op'] ?? '';
    if ($op==='save') {
        $data = [
            'author'     => trim($_POST['author'] ?? ''),
            'quote'      => trim($_POST['quote'] ?? ''),
            'context'    => trim($_POST['context'] ?? ''),
            'rating'     => max(1, min(5, (int)($_POST['rating'] ?? 5))),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ];
        $pid = (int)($_POST['id'] ?? 0);
        if ($pid) {
            $stmt = $pdo->prepare('UPDATE testimonials SET author=:author,quote=:quote,context=:context,rating=:rating,is_active=:is_active,sort_order=:sort_order,updated_at=CURRENT_TIMESTAMP WHERE id=:id');
            $stmt->execute(array_merge($data, ['id'=>$pid]));
            flash('Depoimento atualizado.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO testimonials (author,quote,context,rating,is_active,sort_order) VALUES (:author,:quote,:context,:rating,:is_active,:sort_order)');
            $stmt->execute($data);
            flash('Depoimento criado.');
        }
        header('Location: ' . admin_url('testimonials.php')); exit;
    }
    if ($op==='delete') {
        $pdo->prepare('DELETE FROM testimonials WHERE id=?')->execute([(int)$_POST['id']]);
        flash('Depoimento removido.');
        header('Location: ' . admin_url('testimonials.php')); exit;
    }
}

require __DIR__ . '/partials/layout_top.php';

if ($action==='edit' || $action==='new') {
    $row = ['id'=>0,'author'=>'','quote'=>'','context'=>'','rating'=>5,'is_active'=>1,'sort_order'=>0];
    if ($id) { $stmt = $pdo->prepare('SELECT * FROM testimonials WHERE id=?'); $stmt->execute([$id]); $row = $stmt->fetch() ?: $row; }
    ?>
    <div class="page-head">
      <div><span class="eyebrow"><?= $id?'Editar':'Novo' ?></span><h2><?= $id?'Editar':'Cadastrar'?> <em>depoimento</em>.</h2></div>
      <a href="<?= ee(admin_url('testimonials.php')) ?>" class="btn btn-ghost"><i data-lucide="arrow-left"></i> Voltar</a>
    </div>
    <form method="post" class="adm-card adm-form">
      <?= csrf_field() ?><input type="hidden" name="op" value="save"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
      <div class="row-2">
        <label><span class="lbl">Nome</span><input type="text" name="author" required value="<?= ee($row['author']) ?>"></label>
        <label><span class="lbl">Contexto</span><input type="text" name="context" value="<?= ee($row['context']) ?>" placeholder="Estadia na pousada"></label>
      </div>
      <label><span class="lbl">Depoimento</span><textarea name="quote" required><?= ee($row['quote']) ?></textarea></label>
      <div class="row-2">
        <label><span class="lbl">Avaliação</span><input type="number" min="1" max="5" name="rating" value="<?= (int)$row['rating'] ?>"></label>
        <label><span class="lbl">Ordem</span><input type="number" name="sort_order" value="<?= (int)$row['sort_order'] ?>"></label>
      </div>
      <label style="display:flex; align-items:center; gap:.6rem;"><input type="checkbox" name="is_active" <?= $row['is_active']?'checked':'' ?>> <span>Ativo (visível no site)</span></label>
      <div style="display:flex; gap:.6rem; padding-top:.5rem;">
        <button class="btn btn-primary" type="submit"><i data-lucide="save"></i> Salvar</button>
        <a href="<?= ee(admin_url('testimonials.php')) ?>" class="btn btn-ghost">Cancelar</a>
      </div>
    </form>
    <?php
} else {
    $rows = $pdo->query('SELECT * FROM testimonials ORDER BY sort_order, author')->fetchAll();
    ?>
    <div class="page-head">
      <div><span class="eyebrow">Hóspedes</span><h2>Depoimentos <em>cadastrados</em>.</h2></div>
      <a href="<?= ee(admin_url('testimonials.php?action=new')) ?>" class="btn btn-primary"><i data-lucide="plus"></i> Novo depoimento</a>
    </div>
    <?php if (!$rows): ?>
      <div class="adm-card"><div class="empty"><i data-lucide="quote"></i><h4>Nenhum depoimento cadastrado</h4><p>Adicione relatos de hóspedes para aparecer no site.</p></div></div>
    <?php else: ?>
      <table class="adm-table">
        <thead><tr><th>Nome</th><th>Resumo</th><th>Avaliação</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><strong><?= ee($r['author']) ?></strong><div style="font-size:12px; color:var(--a-muted);"><?= ee($r['context']) ?></div></td>
            <td style="max-width:520px;"><span style="display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"><?= ee($r['quote']) ?></span></td>
            <td><?= str_repeat('★', (int)$r['rating']) ?></td>
            <td><span class="badge <?= $r['is_active']?'success':'muted' ?>"><?= $r['is_active']?'ativo':'oculto' ?></span></td>
            <td style="text-align:right; white-space:nowrap;">
              <a href="<?= ee(admin_url('testimonials.php?action=edit&id='.$r['id'])) ?>" class="btn-icon" title="Editar"><i data-lucide="pencil"></i></a>
              <form method="post" style="display:inline;" onsubmit="return confirm('Remover este depoimento?');">
                <?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn-icon" type="submit" style="color:#9a3a3a;"><i data-lucide="trash-2"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
    <?php
}

require __DIR__ . '/partials/layout_bot.php';