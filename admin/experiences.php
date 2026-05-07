<?php
require __DIR__ . '/bootstrap.php';
require_admin();
$current='experiences.php'; $pageTitle='Experiências'; $pageEyebrow='Gestão';

$pdo = db();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check()) {
    $op = $_POST['op'] ?? '';
    if ($op==='save') {
        $data = [
            'slug'        => slugify($_POST['slug'] ?: $_POST['title']),
            'title'       => trim($_POST['title']),
            'icon'        => trim($_POST['icon'] ?? 'sparkles'),
            'description' => trim($_POST['description'] ?? ''),
            'cover'       => trim($_POST['cover'] ?? ''),
            'gallery'     => trim($_POST['gallery'] ?? ''),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        ];
        if (!empty($_FILES['cover_file']['name'])) {
            if ($p = upload_file($_FILES['cover_file'], 'exp')) $data['cover'] = $p;
        }
        $pid = (int)($_POST['id'] ?? 0);
        if ($pid) {
            $stmt = $pdo->prepare('UPDATE experiences SET slug=:slug,title=:title,icon=:icon,description=:description,cover=:cover,gallery=:gallery,is_active=:is_active,sort_order=:sort_order,updated_at=CURRENT_TIMESTAMP WHERE id=:id');
            $stmt->execute(array_merge($data, ['id'=>$pid])); flash('Experiência atualizada.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO experiences (slug,title,icon,description,cover,gallery,is_active,sort_order) VALUES (:slug,:title,:icon,:description,:cover,:gallery,:is_active,:sort_order)');
            $stmt->execute($data); flash('Experiência criada.');
        }
        header('Location: ' . admin_url('experiences.php')); exit;
    }
    if ($op==='delete') {
        $pdo->prepare('DELETE FROM experiences WHERE id=?')->execute([(int)$_POST['id']]);
        flash('Experiência removida.');
        header('Location: ' . admin_url('experiences.php')); exit;
    }
}

require __DIR__ . '/partials/layout_top.php';

if ($action==='edit' || $action==='new') {
    $row = ['id'=>0,'slug'=>'','title'=>'','icon'=>'flame','description'=>'','cover'=>'','gallery'=>'','is_active'=>1,'sort_order'=>0];
    if ($id) { $stmt = $pdo->prepare('SELECT * FROM experiences WHERE id=?'); $stmt->execute([$id]); $row = $stmt->fetch() ?: $row; }
    ?>
    <div class="page-head">
      <div><span class="eyebrow"><?= $id?'Editar':'Novo' ?></span><h2><?= $id?'Editar':'Cadastrar'?> <em>experiência</em>.</h2></div>
      <a href="<?= ee(admin_url('experiences.php')) ?>" class="btn btn-ghost"><i data-lucide="arrow-left"></i> Voltar</a>
    </div>
    <form method="post" enctype="multipart/form-data" class="adm-card adm-form">
      <?= csrf_field() ?><input type="hidden" name="op" value="save"><input type="hidden" name="id" value="<?= $row['id'] ?>">
      <div class="row-2">
        <label><span class="lbl">Título</span><input type="text" name="title" required value="<?= ee($row['title']) ?>"></label>
        <label><span class="lbl">Ícone Lucide</span><input type="text" name="icon" value="<?= ee($row['icon']) ?>" placeholder="flame, coffee, sprout..."></label>
      </div>
      <label><span class="lbl">Slug (opcional)</span><input type="text" name="slug" value="<?= ee($row['slug']) ?>"></label>
      <label><span class="lbl">Descrição</span><textarea name="description"><?= ee($row['description']) ?></textarea></label>
      <label><span class="lbl">Capa</span></label>
      <?php $name='cover_file'; $current=$row['cover']; $multiple=false; $hint='JPG, PNG ou WEBP'; require __DIR__ . '/partials/upload_zone.php'; ?>
      <label style="margin-top:.6rem;"><span class="lbl" style="font-size:11px; color:var(--a-muted);">Ou cole uma URL externa</span><input type="text" name="cover" value="<?= ee($row['cover']) ?>" placeholder="https://..."></label>
      <label><span class="lbl">Galeria (uma URL por linha)</span><textarea name="gallery" rows="4"><?= ee($row['gallery']) ?></textarea></label>
      <div class="row-2">
        <label style="display:flex; align-items:center; gap:.6rem; padding-top:1.6rem;"><input type="checkbox" name="is_active" <?= $row['is_active']?'checked':'' ?>> Ativo</label>
        <label><span class="lbl">Ordem</span><input type="number" name="sort_order" value="<?= (int)$row['sort_order'] ?>"></label>
      </div>
      <div style="display:flex; gap:.6rem;"><button class="btn btn-primary" type="submit"><i data-lucide="save"></i> Salvar</button><a href="<?= ee(admin_url('experiences.php')) ?>" class="btn btn-ghost">Cancelar</a></div>
    </form>
    <?php
} else {
    $rows = $pdo->query('SELECT * FROM experiences ORDER BY sort_order, title')->fetchAll();
    ?>
    <div class="page-head">
      <div><span class="eyebrow">Vivências</span><h2>Experiências <em>cadastradas</em>.</h2></div>
      <a href="<?= ee(admin_url('experiences.php?action=new')) ?>" class="btn btn-primary"><i data-lucide="plus"></i> Nova experiência</a>
    </div>
    <?php if (!$rows): ?>
      <div class="adm-card"><div class="empty"><i data-lucide="sparkles"></i><h4>Nenhuma experiência cadastrada</h4><p>Adicione Ritual da Fogueira, Chá da Tarde, Mandala etc.</p></div></div>
    <?php else: ?>
      <table class="adm-table">
        <thead><tr><th></th><th>Título</th><th>Ícone</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php if ($r['cover']): ?><img class="thumb" src="<?= ee($r['cover']) ?>" alt=""><?php else: ?><div class="thumb" style="background:#f4ece0;"></div><?php endif; ?></td>
            <td><strong><?= ee($r['title']) ?></strong><div style="font-size:12px; color:var(--a-muted);">/<?= ee($r['slug']) ?></div></td>
            <td><code><?= ee($r['icon']) ?></code></td>
            <td><span class="badge <?= $r['is_active']?'success':'muted' ?>"><?= $r['is_active']?'ativo':'oculto' ?></span></td>
            <td style="text-align:right; white-space:nowrap;">
              <a href="<?= ee(admin_url('experiences.php?action=edit&id='.$r['id'])) ?>" class="btn-icon"><i data-lucide="pencil"></i></a>
              <form method="post" style="display:inline;" onsubmit="return confirm('Remover?');">
                <?= csrf_field() ?><input type="hidden" name="op" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>">
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
