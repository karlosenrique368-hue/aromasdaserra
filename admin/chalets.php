<?php
require __DIR__ . '/bootstrap.php';
require_admin();
$current='chalets.php'; $pageTitle='Chalés'; $pageEyebrow='Gestão';

$pdo = db();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check()) {
    $op = $_POST['op'] ?? '';
    if ($op==='save') {
        $data = [
            'slug'        => slugify($_POST['slug'] ?: $_POST['name']),
            'name'        => trim($_POST['name']),
            'category'    => trim($_POST['category'] ?? ''),
            'view'        => trim($_POST['view'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'cover'       => sanitize_public_image_url((string)($_POST['cover'] ?? '')),
            'gallery'     => sanitize_public_image_items(array_merge((array)($_POST['gallery_urls'] ?? []), upload_gallery_files('gallery_files', 'chalet_gallery'))),
            'video_url'   => sanitize_public_video_url((string)($_POST['video_url'] ?? '')),
            'video_label' => trim($_POST['video_label'] ?? ''),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        ];
        if (!empty($_FILES['cover_file']['name'])) {
            if ($p = upload_file($_FILES['cover_file'], 'chalet')) $data['cover'] = $p;
        }
        $pid = (int)($_POST['id'] ?? 0);
        if ($pid) {
          $stmt = $pdo->prepare('UPDATE chalets SET slug=:slug,name=:name,category=:category,view=:view,description=:description,cover=:cover,gallery=:gallery,video_url=:video_url,video_label=:video_label,is_active=:is_active,sort_order=:sort_order,updated_at=CURRENT_TIMESTAMP WHERE id=:id');
            $stmt->execute(array_merge($data, ['id'=>$pid]));
            flash('Chalé atualizado.');
        } else {
          $stmt = $pdo->prepare('INSERT INTO chalets (slug,name,category,view,description,cover,gallery,video_url,video_label,is_active,sort_order) VALUES (:slug,:name,:category,:view,:description,:cover,:gallery,:video_url,:video_label,:is_active,:sort_order)');
            $stmt->execute($data);
            flash('Chalé criado.');
        }
        header('Location: ' . admin_url('chalets.php')); exit;
    }
    if ($op==='delete') {
        $pdo->prepare('DELETE FROM chalets WHERE id=?')->execute([(int)$_POST['id']]);
        flash('Chalé removido.');
        header('Location: ' . admin_url('chalets.php')); exit;
    }
}

require __DIR__ . '/partials/layout_top.php';

if ($action==='edit' || $action==='new') {
  $row = ['id'=>0,'slug'=>'','name'=>'','category'=>'Standard · Vista jardim','view'=>'','description'=>'','cover'=>'','gallery'=>'','video_url'=>'','video_label'=>'','is_active'=>1,'sort_order'=>0];
    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM chalets WHERE id=?'); $stmt->execute([$id]);
        $row = $stmt->fetch() ?: $row;
    }
    ?>
    <div class="page-head">
      <div><span class="eyebrow"><?= $id?'Editar':'Novo' ?></span><h2><?= $id?'Editar':'Cadastrar'?> <em>chalé</em>.</h2></div>
      <a href="<?= ee(admin_url('chalets.php')) ?>" class="btn btn-ghost"><i data-lucide="arrow-left"></i> Voltar</a>
    </div>
    <form method="post" enctype="multipart/form-data" class="adm-card adm-form">
      <?= csrf_field() ?>
      <input type="hidden" name="op" value="save">
      <input type="hidden" name="id" value="<?= $row['id'] ?>">

      <div class="row-2">
        <label><span class="lbl">Nome</span><input type="text" name="name" required value="<?= ee($row['name']) ?>"></label>
        <label><span class="lbl">Slug (opcional)</span><input type="text" name="slug" value="<?= ee($row['slug']) ?>" placeholder="auto"></label>
      </div>
      <div class="row-2">
        <label><span class="lbl">Categoria</span>
          <select name="category">
            <?php foreach (['Luxo · Vista panorâmica','Luxo VIP · Vista jardim','Standard · Vista jardim'] as $c): ?>
              <option <?= $row['category']===$c?'selected':'' ?>><?= ee($c) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label><span class="lbl">Tipo de vista</span><input type="text" name="view" value="<?= ee($row['view']) ?>" placeholder="Panorâmica para a serra"></label>
      </div>
      <label><span class="lbl">Descrição</span><textarea name="description"><?= ee($row['description']) ?></textarea></label>
      <div class="row-2">
        <label><span class="lbl">Vídeo do chalé</span><input type="text" name="video_url" value="<?= ee($row['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/shorts/..."></label>
        <label><span class="lbl">Texto do botão de vídeo</span><input type="text" name="video_label" value="<?= ee($row['video_label'] ?? '') ?>" placeholder="Ver vídeo do chalé"></label>
      </div>

      <label><span class="lbl">Capa do chalé</span></label>
      <?php $name='cover_file'; $current=$row['cover']; $multiple=false; $hint='JPG, PNG ou WEBP — recomendado 1600×1200'; require __DIR__ . '/partials/upload_zone.php'; ?>
      <label style="margin-top:.6rem;"><span class="lbl" style="font-size:11px; color:var(--a-muted);">Ou cole uma URL externa</span><input type="text" name="cover" value="<?= ee($row['cover']) ?>" placeholder="https://..."></label>

      <?php $items=$row['gallery']; $inputName='gallery_urls[]'; $uploadName='gallery_files[]'; $hint='Organize a galeria por cards. Remova o que não quiser manter e envie novas imagens.'; require __DIR__ . '/partials/gallery_picker.php'; ?>

      <div class="row-2">
        <label style="display:flex; align-items:center; gap:.6rem; padding-top:1.6rem;"><input type="checkbox" name="is_active" <?= $row['is_active']?'checked':'' ?>> <span>Ativo (visível no site)</span></label>
        <label><span class="lbl">Ordem</span><input type="number" name="sort_order" value="<?= (int)$row['sort_order'] ?>"></label>
      </div>

      <div style="display:flex; gap:.6rem; padding-top:.5rem;">
        <button class="btn btn-primary" type="submit"><i data-lucide="save"></i> Salvar</button>
        <a href="<?= ee(admin_url('chalets.php')) ?>" class="btn btn-ghost">Cancelar</a>
      </div>
    </form>
    <?php
} else {
    $rows = $pdo->query('SELECT * FROM chalets ORDER BY sort_order, name')->fetchAll();
    ?>
    <div class="page-head">
      <div><span class="eyebrow">Acomodações</span><h2>Chalés <em>cadastrados</em>.</h2></div>
      <a href="<?= ee(admin_url('chalets.php?action=new')) ?>" class="btn btn-primary"><i data-lucide="plus"></i> Novo chalé</a>
    </div>
    <?php if (!$rows): ?>
      <div class="adm-card"><div class="empty"><i data-lucide="home"></i><h4>Nenhum chalé cadastrado</h4><p>Comece adicionando os chalés Lavanda, Manjericão e Aromáticos.</p></div></div>
    <?php else: ?>
      <table class="adm-table">
        <thead><tr><th></th><th>Nome</th><th>Categoria</th><th>Vista</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php if ($r['cover']): ?><img class="thumb" src="<?= ee($r['cover']) ?>" alt=""><?php else: ?><div class="thumb" style="background:#f4ece0;"></div><?php endif; ?></td>
            <td><strong><?= ee($r['name']) ?></strong><div style="font-size:12px; color:var(--a-muted);">/<?= ee($r['slug']) ?></div></td>
            <td><?= ee($r['category']) ?></td>
            <td><?= ee($r['view']) ?></td>
            <td><span class="badge <?= $r['is_active']?'success':'muted' ?>"><?= $r['is_active']?'ativo':'oculto' ?></span></td>
            <td style="text-align:right; white-space:nowrap;">
              <a href="<?= ee(admin_url('chalets.php?action=edit&id='.$r['id'])) ?>" class="btn-icon" title="Editar"><i data-lucide="pencil"></i></a>
              <form method="post" style="display:inline;" onsubmit="return confirm('Remover este chalé?');">
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
